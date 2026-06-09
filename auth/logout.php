<?php
require_once __DIR__ . '/../app/helpers.php';

session_destroy();
session_start();
flash('success', 'Đã đăng xuất.');
redirect('index.php');
