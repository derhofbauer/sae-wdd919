<div class="product row">
    <?php
    /**
     * [ ] Cart Inhalt
     */
    ?>

    <form action="cart/update" method="post" class="col cart-content">
        <?php if (!empty($products)): ?>
            <?php require_once __DIR__ . '/../partials/products-table.php'; ?>
            <div class="cart-buttons">
                <?php if (\App\Models\User::isLoggedIn()): ?>
                    <a class="btn btn-primary" href="checkout">Checkout</a>
                <?php else: ?>
                    <a class="btn btn-primary" href="login">Login to proceed</a>
                <?php endif; ?>
                <button class="btn" type="submit">Update</button>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Du hast noch keine Produkte im Warenkorb :(</div>
        <?php endif; ?>
    </form>

</div>
