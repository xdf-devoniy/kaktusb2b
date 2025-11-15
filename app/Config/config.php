<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('APP_PATH')) {
    define('APP_PATH', realpath(__DIR__ . '/..'));
}

const DB_PATH = __DIR__ . '/../../database/kaktus.sqlite';
const APP_NAME = 'Kaktus B2B';
const COMPANY_BRAND = 'Kaktus Profi';
const COMPANY_SERVICE = 'Natyajny Potolok';
const COMPANY_PHONE = '62-226-96-96';
const COMPANY_LOCATION = "Xorazm, Urganch";

spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/..';
    $class = str_replace('\\', '/', $class);
    $paths = [
        $baseDir . '/Core/' . basename($class) . '.php',
        $baseDir . '/Models/' . basename($class) . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
        }
    }
});

require_once __DIR__ . '/../Core/Helpers.php';

$database = new Database(DB_PATH);
