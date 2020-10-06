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
        $stmt = $link->prepare("SELECT * FROM users");
        $stmt->execute();
        /**
         * Datenbank Ergebnis in ein assoziatives Array umwandeln, damit wir damit ordentlich arbeiten können
         */
        $usersResult = $stmt->get_result();
        $usersData = $usersResult->fetch_all(MYSQLI_ASSOC);

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
            $stmt = $link->prepare("SELECT topics.id, topics.name FROM abos JOIN topics ON abos.topic_id = topics.id WHERE abos.user_id = ?");
            $stmt->bind_param('i', $user['id']);
            $stmt->execute();
            $topicsResult = $stmt->get_result();
            $topicsData = $topicsResult->fetch_all(MYSQLI_ASSOC);

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
