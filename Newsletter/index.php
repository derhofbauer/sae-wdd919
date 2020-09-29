<?php

/**
 * Tasks:
 *
 * [ ] Mehrere Topics
 * [ ] Email + Name speichern
 * [ ] Abos-Übersicht
 * [ ] Übersicht über alle Topics
 * [ ] Abo-Formular
 *
 * ----
 *
 * [x] Tabellen definieren (Tabellen: Users, Abos, Topics)
 * [x] Spalten definieren User: ID, Nachname, Vorname, E-Mail
 * [x] Spalten definieren Abo: ID, Topic ID, User ID
 * [x] Spalten definieren Topic: ID, Name, Beschreibung
 *
 */

require_once 'dbconnect.php';
require_once 'partials/header.php';
?>

<div class="container">
    <div class="row topics">
        <?php
        /**
         * [ ] Liste der Topics
         */
        ?>
    </div>
    <div class="row form">
        <?php
        /**
         * Abo-Formular
         * [ ] Auswahl der Topics
         * [ ] Email, Vorname, Nachname
         */
        ?>
    </div>
</div>

<?php
require_once 'partials/footer.php';
?>
