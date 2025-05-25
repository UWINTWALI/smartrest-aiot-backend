<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = new App\Models\User();
echo "Primary key: " . $user->getKeyName() . PHP_EOL;
echo "Key type: " . $user->getKeyType() . PHP_EOL;
echo "Incrementing: " . ($user->getIncrementing() ? 'true' : 'false') . PHP_EOL;
