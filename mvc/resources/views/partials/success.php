<?php

/**
 * s. partials/error.php
 */
$success = \Core\Session::getAndForget('success', []);

foreach ($success as $message): ?>

    <p class="alert alert-success"><?php echo $message; ?></p>

<?php endforeach; ?>
