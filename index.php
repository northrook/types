<?php

use Northrook\Logger\Log;
use Northrook\Types\Email;
use Northrook\Types\Password;

require __DIR__ . '/vendor/autoload.php';

$cases = [
    'password',
    'password123',
    'password123!',
    'Password123!',
    'Password2024',
    '!Password2024!',
    '5f4dcc3b5aa765d61d8327deb882cf99',
];

$password = new Password(
    $cases[ 5 ],
//    4
);

$email = new Email(
    'hello@northrook.com'
);

echo $password->print();
dd(
    $email->isValid,
    $email,
    $password->isValid,
    $password->validator,
    $password,
    Log::inventory(),
);