<?php

namespace App\Controllers;

use Core\View;

/**
 * @todo: comment
 */
class ApiDemoController
{

    /**
     * @todo: comment
     */
    public function colors ()
    {
        $result = file_get_contents('https://reqres.in/api/unknown');
        $data = json_decode($result);
        View::render('api-demos/colors', [
            'colors' => $data->data
        ]);
    }

}
