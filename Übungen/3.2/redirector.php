<?php

session_start();

if (isset($_GET['url'])) {
    if (!isset($_SESSION['visited']) || !is_array($_SESSION['visited'])) {
        $_SESSION['visited'] = [];
    }

    $url = $_GET['url'];
    if (array_key_exists($_GET['url'], $_SESSION['visited'])) {
        $_SESSION['visited'][$url] += 1;
    } else {
        $_SESSION['visited'][$url] = 1;
    }
}

header("Location: " . $_GET['url']);
