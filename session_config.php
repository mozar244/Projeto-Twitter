<?php
// session_config.php

$project_root = __DIR__;
$session_path = $project_root . '/sessions';

if (!is_dir($session_path)) {
    mkdir($session_path, 0755, true);
}

session_save_path($session_path);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Lax');

session_start();
?>