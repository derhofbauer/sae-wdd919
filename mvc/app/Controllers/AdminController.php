<?php

namespace App\Controllers;

use App\Models\User;

/**
 * Class AdminController
 *
 * @package App\Controllers
 * @todo    : comment
 */
class AdminController
{

    public function test ()
    {
        if (User::isLoggedIn() && User::getLoggedIn()->is_admin) {
            echo "die methode wurde aufgerufen!";
        } else {
            echo "du darfst diese Route nicht aufrufen";
        }
    }

}
