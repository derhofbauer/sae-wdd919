<?php

session_start();

if (isset($_GET['url'])) {
    if (!isset($_SESSION['visited']) || !is_array($_SESSION['visited'])) {
        $_SESSION['visited'] = [];
    }

    if (!in_array($_GET['url'], $_SESSION['visited'])) {
        $_SESSION['visited'][] = $_GET['url'];
    }
}

header("Location: " . $_GET['url']);
