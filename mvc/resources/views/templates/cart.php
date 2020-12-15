<div class="product row">
    <?php
    /**
     * [ ] Cart Inhalt
     */
    ?>

    <form action="cart/update" method="post" class="col cart-content">
        <?php require_once __DIR__ . '/../partials/products-table.php'; ?>
        <?php if (!empty($products)): ?>
            <div class="cart-buttons">
                <?php if (\App\Models\User::isLoggedIn()): ?>
                    <a class="btn btn-primary" href="checkout">Checkout</a>
                <?php else: ?>
                    <a class="btn btn-primary" href="login">Login to proceed</a>
                <?php endif; ?>
                <button class="btn" type="submit">Update</button>
            </div>
        <?php endif; ?>
    </form>

</div>
