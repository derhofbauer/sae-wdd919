<?php
/**
 * Datenbankverbindung und Header einbinden
 */
require_once 'dbconnect.php';
require_once 'partials/header.php';
?>

<div class="container">
    <ul>
        <?php
        /**
         * Alles aus der user-Tabelle abfragen
         */
        $usersResult = mysqli_query($link, "SELECT * FROM users");
        /**
         * Datenbank Ergebnis in ein assoziatives Array umwandeln, damit wir damit ordentlich arbeiten können
         */
        $usersData = mysqli_fetch_all($usersResult, MYSQLI_ASSOC);

        /**
         * Alle User*innen in einer Schleife durchgehen
         */
        foreach ($usersData as $user) {
            /**
             * Für jede*n User*in geben wir ein `<li>` aus und darin ein `<ul>` mit den abonnierten Topics
             */
            echo "<li>" . $user['email'] . "<ul>";

            /**
             * Alle Topics für den/die aktuelle User*in aus der Datenbank mithilfe eines JOINs über die abos und topics
             * Tabellen abfragen und in ein assoziatives Array umwandeln
             */
            $topicsResult = mysqli_query($link, "SELECT topics.id, topics.name FROM abos JOIN topics ON abos.topic_id = topics.id WHERE abos.user_id = '" . $user['id'] . "'");
            $topicsData = mysqli_fetch_all($topicsResult, MYSQLI_ASSOC);

            /**
             * Alle Topics des/der User*in durchgehen und in einem `<li>` ausgeben
             */
            foreach ($topicsData as $topic) {
                echo "<li>#" . $topic['id'] . ": " . $topic['name'] . "</li>";
            }

            echo "</ul></li>";
        }

        ?>
    </ul>
</div>

<?php
/**
 * Footer einbinden
 */
require_once 'partials/footer.php';
?>
