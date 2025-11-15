<?php
require_once __DIR__ . '/../app/Config/config.php';
Auth::logout();
redirect('login.php');
