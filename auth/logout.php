<?php
require_once __DIR__ . '/../app/helpers.php';

session_destroy();
session_start();
flash('success', 'Da dang xuat.');
redirect('index.php');
