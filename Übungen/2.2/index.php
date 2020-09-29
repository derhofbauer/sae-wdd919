<?php
require_once 'partials/header.php';
require_once 'partials/nav.php';
?>

<?php

/**
 * $_GET auslesen
 * - index.php?page=home --> content/home.php
 * - index.php?page=blog --> content/blog.php
 * - ...
 */

if (isset($_GET['page'])) {
    if ($_GET['page'] === 'home') {
        require_once 'content/home.php';
    } elseif ($_GET['page'] === 'blog') {
        require_once 'content/blog.php';
    } elseif ($_GET['page'] === 'contact') {
        require_once 'content/contact.php';
    } elseif ($_GET['page'] === 'contact-validate') {
        require_once 'validators/contact-validate.php';
    } else {
        require_once 'content/404.php';
    }
} else {
    require_once 'content/home.php';
}

?>

<?php
require_once 'partials/footer.php';
?>
