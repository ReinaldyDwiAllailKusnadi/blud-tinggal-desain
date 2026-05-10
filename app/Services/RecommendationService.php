<?php

namespace App\Services;

use App\Models\Content;
use App\Models\Event;
use App\Models\Submission;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Service untuk menghitung rekomendasi lokasi menggunakan metode Knowledge-Based Recommendation.
 * Pendekatan yang digunakan adalah similarity matching antara kriteria user dengan karakteristik lokasi.
 */
class RecommendationService
{
    public function recommend(array $criteria): array
    {
        $eventType = $criteria['event_type'] ?? null;
        $participants = $criteria['participants'] ?? null;
        $dateInput = $criteria['date'];
        $budget = $criteria['budget'] ?? null;
        $requestedFacilities = $criteria['facilities'] ?? [];
        $preference = $criteria['preference'] ?? null;

        $targetDate = Carbon::parse($dateInput);
        $isWeekend = $targetDate->isWeekend();

        // 1. Ambil semua data lokasi beserta fiturnya
        $contents = Content::with(['features'])->get();

        $results = [];

        foreach ($contents as $content) {
            $score = 0;
            $available = true;
            $reasons = [];
            $matchedFacilities = [];

            // A. Cek Ketersediaan Tanggal (Constraint Wajib)
            // Cek di tabel event (jadwal luar)
            $isEventConflict = Event::where('location', $content->name)
                ->whereDate('start_date', '<=', $targetDate->format('Y-m-d'))
                ->whereDate('end_date', '>=', $targetDate->format('Y-m-d'))
                ->exists();

            // Cek di tabel submission yang sudah approved (booking internal)
            $isSubmissionConflict = Submission::where('location', $content->name)
                ->where('status', 'approved')
                ->whereDate('start_date', '<=', $targetDate->format('Y-m-d'))
                ->whereDate('end_date', '>=', $targetDate->format('Y-m-d'))
                ->exists();

            if ($isEventConflict || $isSubmissionConflict) {
                $available = false;
                $reasons[] = "Lokasi tidak tersedia pada tanggal yang dipilih (" . $targetDate->format('d M Y') . ")";
            } else {
                $reasons[] = "Tanggal tersedia";
            }

            // Jika tersedia, hitung similarity
            if ($available) {
                // 1. Sbudget (Bobot 0.30)
                $price = $this->determinePrice($content, $isWeekend);
                $sBudget = 0.5; // Default if price null
                if ($price !== null && $budget !== null) {
                    if ($price <= $budget) {
                        $sBudget = 1;
                        $reasons[] = "Harga sewa (Rp " . number_format($price, 0, ',', '.') . ") sesuai dengan budget Anda";
                    } else {
                        $sBudget = max(0, 1 - (($price - $budget) / $price));
                        $reasons[] = "Harga sewa melebihi budget, namun masih dipertimbangkan";
                    }
                } elseif ($price !== null) {
                    $reasons[] = "Harga sewa: Rp " . number_format($price, 0, ',', '.');
                }

                // 2. Sfasilitas (Bobot 0.30)
                $sFasilitas = 1;
                if (!empty($requestedFacilities)) {
                    $locationFacilities = $content->features->where('type', 'facility')->pluck('facility_name')->toArray();
                    $matchCount = 0;
                    foreach ($requestedFacilities as $req) {
                        foreach ($locationFacilities as $loc) {
                            if (Str::contains(strtolower($loc), strtolower($req)) || Str::contains(strtolower($req), strtolower($loc))) {
                                $matchCount++;
                                $matchedFacilities[] = $loc;
                                break;
                            }
                        }
                    }
                    $sFasilitas = $matchCount / count($requestedFacilities);
                    if ($matchCount > 0) {
                        $reasons[] = "Fasilitas yang sesuai: " . implode(', ', array_unique($matchedFacilities));
                    }
                }

                // 3. Skapasitas (Bobot 0.25)
                $sKapastas = 0.5;
                if ($content->capacity !== null && $participants !== null) {
                    if ($content->capacity >= $participants) {
                        $sKapastas = 1;
                        $reasons[] = "Kapasitas mencukupi untuk $participants peserta";
                    } else {
                        $sKapastas = max(0, $content->capacity / $participants);
                        $reasons[] = "Kapasitas (" . $content->capacity . ") kurang mencukupi untuk $participants peserta";
                    }
                }

                // 4. Sjenis (Bobot 0.15)
                $sJenis = 0;
                $prefMatch = false;
                if ($preference) {
                    if (Str::contains(strtolower($preference), 'indoor') && $content->is_indoor) $prefMatch = true;
                    if (Str::contains(strtolower($preference), 'outdoor') && $content->is_outdoor) $prefMatch = true;
                    if ($content->venue_type && Str::contains(strtolower($content->venue_type), strtolower($preference))) $prefMatch = true;
                }

                if ($prefMatch) {
                    $sJenis = 1;
                    $reasons[] = "Tipe lokasi sesuai dengan preferensi " . ($preference ?? '');
                } else {
                    // Logic tambahan berdasarkan event_type
                    if ($eventType) {
                        $et = strtolower($eventType);
                        if ((Str::contains($et, 'seminar') || Str::contains($et, 'rapat')) && $content->is_indoor) {
                            $sJenis = 0.8;
                            $reasons[] = "Lokasi indoor cocok untuk kegiatan " . $eventType;
                        } elseif ((Str::contains($et, 'outbound') || Str::contains($et, 'bazar') || Str::contains($et, 'pentas')) && $content->is_outdoor) {
                            $sJenis = 0.8;
                            $reasons[] = "Lokasi outdoor cocok untuk kegiatan " . $eventType;
                        }
                    }
                }

                // Hitung Skor Akhir Similarity
                $similarity = (0.30 * $sBudget) + (0.30 * $sFasilitas) + (0.25 * $sKapastas) + (0.15 * $sJenis);
                $score = round($similarity * 100, 1);
            } else {
                $score = 0;
            }

            // Tentukan Status
            $status = "Kurang Direkomendasikan";
            if (!$available) {
                $status = "Tidak Tersedia";
            } elseif ($score >= 85) {
                $status = "Sangat Direkomendasikan";
            } elseif ($score >= 70) {
                $status = "Direkomendasikan";
            } elseif ($score >= 50) {
                $status = "Cukup Sesuai";
            }

            $results[] = [
                "id" => $content->id,
                "name" => $content->name,
                "slug" => $content->slug,
                "score" => $score,
                "status" => $status,
                "available" => $available,
                "price" => $this->determinePrice($content, $isWeekend),
                "capacity" => $content->capacity,
                "venue_type" => $content->venue_type,
                "is_indoor" => (bool) $content->is_indoor,
                "is_outdoor" => (bool) $content->is_outdoor,
                "image" => $content->image ? url($content->image) : null,
                "matched_facilities" => array_unique($matchedFacilities),
                "reasons" => $reasons
            ];
        }

        // Urutkan: Tersedia dulu, lalu Skor tertinggi
        usort($results, function ($a, $b) {
            if ($a['available'] && !$b['available']) return -1;
            if (!$a['available'] && $b['available']) return 1;
            return $b['score'] <=> $a['score'];
        });

        return $results;
    }

    /**
     * Menentukan harga lokasi berdasarkan ketersediaan data di tabel content atau content_features.
     */
    private function determinePrice($content, $isWeekend)
    {
        // 1. Cek price dari tabel content
        $priceField = $isWeekend ? 'price_weekend' : 'price_weekday';
        if (!empty($content->$priceField) && is_numeric($content->$priceField)) {
            return (int) $content->$priceField;
        }

        // 2. Jika tidak ada, ambil harga terendah dari content_features (type=price)
        $featurePrice = $content->features->where('type', 'price')->min('price');
        if ($featurePrice) {
            return (int) $featurePrice;
        }

        return null;
    }
}
