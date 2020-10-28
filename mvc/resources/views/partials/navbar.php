<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="<?php echo \Core\Config::get('app.baseurl'); ?>">Home</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <?php
                /**
                 * Hier laden wir die statische Funktion numberOfProducts() aus dem CartController, damit wir die Summe
                 * der Produkte aus dem Cart angeben können. Die Methode ist deshalb statisch, damit wir sie hier direkt
                 * aus der Klasse aufrufen können ohne vorher ein Objekt erstellen zu müssen.
                 */
                ?>
                <a class="nav-link" href="cart">
                    Cart (<?php echo \App\Controllers\CartController::numberOfProducts(); ?>)
                </a>
            </li>
        </ul>

        <ul class="navbar-nav">

            <?php
            /**
             * Ist ein*e User*in eingeloggt und ein Admin, so kriegt die Person den Dashboard Link angezeigt. Ist sie
             * kein Admin, so kriegt sie nur den Logout link.
             */
            if (\App\Models\User::isLoggedIn()): ?>
                <?php if (\App\Models\User::getLoggedIn()->is_admin === true): ?>
                    <li class="nav-item">
                        <a href="admin" class="nav-link">Dashboard</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a href="logout" class="nav-link">
                        <?php echo \App\Models\User::getLoggedIn()->username; ?>, Logout
                    </a>
                </li>
            <? else: ?>
                <li class="nav-item">
                    <a href="login" class="nav-link">Login</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
