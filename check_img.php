<?php
$url = "https://bludtesting.my.id/assets/img/bg.png";
$headers = get_headers($url, 1);
print_r($headers);
