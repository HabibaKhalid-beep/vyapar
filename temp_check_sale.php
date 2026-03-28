<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use App\Models\Sale;

$sale = Sale::find(4);
var_dump($sale);
