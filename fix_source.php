<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$news = App\Models\News::first();
if ($news) {
    $news->source = str_replace('https://127.0.0.1:8000', 'http://127.0.0.1:8000', $news->source);
    $news->save();
    echo "SOURCE UPDATED TO: " . $news->source . "\n";
}
