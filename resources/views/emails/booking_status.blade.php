<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Pengajuan Booking</title>
</head>
<body>
    <p>Halo, {{ $submission->user->name }}</p>

    @if ($status === 'approved')
        <p>Dengan hormat,</p>
        <p>Kami informasikan bahwa pengajuan booking Anda dengan vendor <b>{{ $submission->vendor }}</b> telah <strong style="color:green;">DISETUJUI ✅</strong>.</p>

        <p><b>Detail Pengajuan:</b></p>
        <ul>
            <li>Vendor: {{ $submission->vendor }}</li>
            <li>Nama Event: {{ $submission->name_event }}</li>
            <li>Lokasi:  {{ $submission->location}}</li>
            <li>Tanggal: {{ $submission->start_date }} - {{$submission->end_date}}</li>
        </ul>

        <p>Silakan membawa bukti konfirmasi ini saat proses lebih lanjut.</p>
        <p>Terima kasih atas kepercayaan Anda kepada <b>BLUD Pariwisata</b>.</p>
    @elseif ($status === 'rejected')
        <p>Dengan hormat,</p>
        <p>Kami sampaikan bahwa pengajuan booking Anda dengan vendor <b>{{ $submission->vendor }}</b> dinyatakan <strong style="color:red;">DITOLAK ❌</strong>.</p>

        <p><b>Alasan Penolakan:</b></p>
        <blockquote style="border-left: 3px solid #ccc; padding-left: 10px; color: #555;">
            {{ $submission->notes }}
        </blockquote>

        <p>Kami mohon maaf atas ketidaknyamanan ini. Anda dapat melakukan pengajuan kembali dengan menyesuaikan ketentuan yang berlaku.</p>
        <p>Terima kasih atas perhatian dan pengertian Anda.</p>
    @endif

    <br>
    <p>Hormat kami,</p>
    <p><strong>Tim BLUD Pariwisata</strong></p>
</body>
</html>
