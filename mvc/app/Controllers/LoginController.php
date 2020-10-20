<?php

namespace App\Controllers;

use Core\View;

/**
 * Class LoginController
 *
 * @package App\Controllers
 */
class LoginController
{

    /**
     * Loin Formular anzeigen
     */
    public function loginForm ()
    {
        View::render('login');
    }

    /**
     * Login durchführen
     *
     * @todo: comment
     */
    public function doLogin ()
    {
        /**
         * [ ] Validierung
         * [ ] Login durchführen
         * [ ] Redirect zurück (Referrer)
         */
    }

}
