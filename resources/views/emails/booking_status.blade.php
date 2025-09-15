<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Status Pengajuan Booking</title>
</head>
<body>
    <h2>Halo, {{ $submission->user->name }}</h2>

    @if($status === 'approved')
        <p>Selamat! 🎉</p>
        <p>Pengajuan booking Anda dengan vendor <b>{{ $submission->vendor }}</b> telah <strong>DISETUJUI</strong>.</p>
        <p>Detail Acara:</p>
        <ul>
            <li>Nama Event: {{ $submission->name_event }}</li>
            <li>Lokasi: {{ $submission->location }}</li>
            <li>Tanggal: {{ $submission->start_date }} - {{ $submission->end_date }}</li>
        </ul>
    @else
        <p>Mohon maaf 🙏</p>
        <p>Pengajuan booking Anda dengan vendor <b>{{ $submission->vendor }}</b> <strong>DITOLAK</strong>.</p>
        <p>Alasan Penolakan:</p>
        <blockquote>{{ $submission->notes }}</blockquote>
    @endif

    <p>Terima kasih,</p>
    <p><strong>BLUD Pariwisata</strong></p>
</body>
</html>
