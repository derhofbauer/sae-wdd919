<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="<?php echo \Core\Config::get('app.baseurl'); ?>">Home</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <?php foreach (\App\Models\Category::all() as $navbarCategory): ?>
                <li class="nav-item">
                    <a href="products/category/<?php echo $navbarCategory->id; ?>" class="nav-link"><?php echo $navbarCategory->name; ?></a>
                </li>
            <?php endforeach; ?>

            <li class="nav-item">
                <a href="contact" class="nav-link">Contact</a>
            </li>
            <li class="nav-item">
                <a href="blog" class="nav-link">Blog</a>
            </li>
            <?php if (\App\Models\User::isLoggedIn()): ?>
                <li class="nav-item">
                    <a href="wishlist" class="nav-link">Wishlist</a>
                </li>
            <?php endif; ?>
        </ul>

        <form class="form-inline my-2 my-lg-0" method="get" action="search">
            <?php
            $value = '';
            if (isset($_GET['searchterm']) && !empty($_GET['searchterm'])) {
                $value = $_GET['searchterm'];
            }
            ?>
            <input class="form-control mr-sm-2" type="search" placeholder="Produktsuche ..." aria-label="Search" name="searchterm" value="<?php echo $value; ?>">
            <button class="btn btn-outline-primary my-2 my-sm-0" type="submit">Search</button>
        </form>

        <ul class="navbar-nav">
            <li class="nav-item popup-container">
                <?php
                /**
                 * Hier laden wir die statische Funktion numberOfProducts() aus dem CartController, damit wir die Summe
                 * der Produkte aus dem Cart angeben können. Die Methode ist deshalb statisch, damit wir sie hier direkt
                 * aus der Klasse aufrufen können ohne vorher ein Objekt erstellen zu müssen.
                 */
                ?>
                <a class="nav-link" href="cart">
                    Cart
                    <span class="number-of-products">(<?php echo \App\Controllers\CartController::numberOfProducts(); ?>)</span>
                </a>
                <?php require_once __DIR__ . '/../partials/cart-popup.php'; ?>
            </li>

            <?php
            /**
             * Ist ein*e User*in eingeloggt und ein Admin, so kriegt die Person den Dashboard Link angezeigt. Ist sie
             * kein Admin, so kriegt sie nur den Logout link.
             */
            if (\App\Models\User::isLoggedIn()): ?>
                <li class="nav-item">
                    <a href="profile/orders" class="nav-link">Orders</a>
                </li>
                <li class="nav-item">
                    <a href="profile" class="nav-link">
                        Edit Profile (<?php echo \App\Models\User::getLoggedIn()->username; ?>)
                    </a>
                </li>
                <?php if (\App\Models\User::getLoggedIn()->is_admin === true): ?>
                    <li class="nav-item">
                        <a href="admin" class="nav-link">Dashboard</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a href="logout" class="nav-link">Logout</a>
                </li>
            <? else: ?>
                <li class="nav-item">
                    <a href="login" class="nav-link">Login</a>
                </li>
                <li class="nav-item">
                    <a href="sign-up" class="nav-link">Sign-up</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
