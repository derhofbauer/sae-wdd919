<?php

/**
 * https://www.php.net/manual/de/function.mysqli-connect.php
 *
 * MAMP (Macs): mysqli_connect('localhost', 'root', 'root', 'newsletter', 8889)
 *
 * Hier wird die Datenbankverbindung hergestellt. $link beinhaltet dann ein Objekt, das es uns erlaubt, mit der MySQL-
 * Datenbank zu interagieren.
 */
$link = mysqli_connect('mariadb', 'root', 'password', 'newsletter');
