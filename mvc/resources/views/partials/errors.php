<?php

/**
 * @todo: comment
 */
$errors = \Core\Session::getAndForget('errors', []);

foreach ($errors as $error): ?>

    <p class="alert alert-danger"><?php echo $error; ?></p>

<?php endforeach; ?>
