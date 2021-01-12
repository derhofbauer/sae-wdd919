<?php

namespace App\Controllers;

use Core\View;

/**
 * Class ApiDemoController
 *
 * @package App\Controllers
 */
class ApiDemoController
{

    /**
     * Das ist eine Demo Funktion, um einmal mit PHP Daten von einer API abgerufen zu haben.
     */
    public function colors ()
    {
        /**
         * JSON Daten von einer Testing-API holen.
         */
        $result = file_get_contents('https://reqres.in/api/unknown');

        /**
         * JSON String in ein Objekt dekodieren.
         */
        $data = json_decode($result);

        /**
         * View laden und Daten übergeben.
         */
        View::render('api-demos/colors', [
            'colors' => $data->data
        ]);
    }

}
