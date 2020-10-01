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

/**
 * Datenbankverbindung und Header einbinden
 */
require_once 'dbconnect.php';
require_once 'partials/header.php';
?>

<div class="container">

    <?php

    /**
     * Gibt es den GET-Parameter success, geben wir eine Erfolgsmeldung aus
     */
    if (isset($_GET['success'])) {
        /**
         * Zur personalisierung der Erfolgsmeldung haben die in den GET-Parameter die Email-Adresse geschrieben
         */
        $successEmail = $_GET['success'];

        echo '<div class="alert alert-success"> ' . $successEmail . " was subscribed successfully!</div>";
    }

    ?>

    <div class="row">
        <div class="row topics col-6">
            <div class="list-group">
                <?php
                /**
                 * [x] Liste der Topics
                 */

                /**
                 * Alle Topics aus der Datenbank abfragen
                 */
                $result = mysqli_query($link, "SELECT * FROM topics");

                /**
                 * Datenbank-Ergebnis Zeile für Zeile durchgehen
                 *
                 * Die while-Schleife funktioniert deshalb, weil mysqli_fetch_assoc() false zurück gibt, wenn keine
                 * weitere Zeile zum durchgehen mehr verfügbar ist.
                 *
                 * Die Körper von Schleifen und Bedingungen können mit {} umschlossen werden oder mit einem Doppelpunkt
                 * und dem zugehörigen end*-Statement.
                 */
                while ($row = mysqli_fetch_assoc($result)): ?>

                    <div class="list-group-item">
                        <h5><?php echo $row['name']; ?></h5>
                        <p><?php echo $row['description']; ?></p>
                    </div>

                <?php endwhile; ?>
            </div>
        </div>
        <div class="row form col-6">
            <?php
            /**
             * Abo-Formular
             * [x] Auswahl der Topics
             * [x] Email, Vorname, Nachname
             */
            ?>
            <form action="subscribe.php" method="post" class="row">
                <div class="form-group col-6">
                    <label for="firstname">Firstname</label>
                    <input type="text" name="firstname" id="firstname" class="form-control">
                </div>
                <div class="form-group col-6">
                    <label for="lastname">Lastname</label>
                    <input type="text" name="lastname" id="lastname" class="form-control">
                </div>
                <div class="form-group col-6">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control">
                </div>
                <div class="form-group form-check col-6">
                    <label for="topics">Topics</label>

                    <?php
                    /**
                     * Alle Topics aus der Datenbank abfragen
                     */
                    $result = mysqli_query($link, "SELECT * FROM topics");

                    /**
                     * Datenbankergebnis in ein assoziatives Array umwandeln
                     */
                    $resultData = mysqli_fetch_all($result, MYSQLI_ASSOC);

                    foreach ($resultData as $topic): ?>

                        <div>
                            <label>
                                <!-- Das name-Attribut wird hier mit [<wert>] angegeben, damit PHP es als Array darstellt innerhalb der GET- oder POST-Parameter -->
                                <input type="checkbox" name="topics[<?php echo $topic['id']; ?>]" id="topic-<?php echo $topic['id']; ?>"> <?php echo $topic['name']; ?>
                            </label>
                        </div>

                    <?php endforeach; ?>
                </div>
                <div class="form-group col-12">
                    <button type="submit" class="btn btn-primary">Subscribe</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once 'partials/footer.php';
?>
