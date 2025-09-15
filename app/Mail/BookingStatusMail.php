<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Submission;

class BookingStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $submission;
    public $status;

    public function __construct(Submission $submission, $status)
    {
        $this->submission = $submission;
        $this->status = $status;
    }

    public function build()
    {
        return $this->subject('Status Pengajuan Booking')
                    ->view('emails.booking_status')
                    ->with([
                        'submission' => $this->submission,
                        'status' => $this->status,
                    ]);
    }
}