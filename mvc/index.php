<?php

/**
 * Der Webroot der Anwendung sollte auf /mvc/public/ zeigen, aber nachdem das im MAMP nicht so einfach zu konfigurieren
 * ist, definieren wir hier eine Hilfsdatei, die einfach nur das eigentlich index.php File aus dem /public Ordner lädt.
 */

require_once 'public/index.php';
