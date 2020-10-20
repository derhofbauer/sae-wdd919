<?php

namespace App\Controllers;

use Core\View;

/**
 * Class LoginController
 *
 * @package App\Controllers
 * @todo    : comment
 */
class LoginController
{

    public function loginForm ()
    {
        View::render('login');
    }

    public function doLogin ()
    {
        /**
         * [ ] Validierung
         * [ ] Login durchführen
         * [ ] Redirect zurück (Referrer)
         */
    }

}
