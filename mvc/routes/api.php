<?php

/**
 * Die Dateien im /routes Ordner beinhalten ein Mapping von einer URL auf eine eindeutige Controller & Action
 * kombination. Als Konvention definieren wir, dass URL-Parameter mit {xyz} definiert werden müssen, damit das Routing
 * korrekt funktioniert.
 *
 * Routen innerhalb des /routes/api.php Files erhalten das Präfix /api/, damit klar ist, es handelt sich um Routen, die
 * keine Views laden, sondern lediglich Daten zurückgeben oder empfangen.
 */
return [
  // '/some/api/route' => [AjaxController::class, 'something']
];
