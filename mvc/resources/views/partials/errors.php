<?php

/**
 * Damit wir Fehler immer im nächsten Request ausgeben können, schreiben wir sie in den Actions, in denen sie auftreten,
 * in die Session und verwenden immer den selben $key, nämlich 'errors'. Dieses Partial können wir dann überall dort
 * verwenden, wo die Fehler aus dem vorhergehenden Request angezeigt werden sollen. Mit Session::getAndForget() holen
 * wir die Fehler mit dem $key 'errors' aus der Session und löschen den Wert anschließend aus der Session.
 */
$errors = \Core\Session::getAndForget('errors', []);

/**
 * Hier gehen wir alle Fehler, die wir zuvor aus der Session ausgelesen haben, durch.
 */
foreach ($errors as $error): ?>

    <p class="alert alert-danger"><?php echo $error; ?></p>

<?php endforeach; ?>
