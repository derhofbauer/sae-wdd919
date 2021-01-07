<div class="cart-popup">
    <!--Name, Bild, Checkout-Button, Anzahl-->
    <div class="popup-content">
        <?php
        [$cartContent, $total] = \App\Controllers\CartController::getCartContent();
        foreach ($cartContent as $cartContentProduct): ?>
            <div class="popup-item">
                <?php if (count($cartContentProduct->getImages()) > 0): ?>
                    <img src="<?php echo $cartContentProduct->getImages()[0]; ?>" alt="<?php echo $cartContentProduct->name ?>" class="img-thumbnail">
                <?php endif; ?>
                <span class="quantity"><?php echo $cartContentProduct->quantity; ?>x</span>
                <span class="name"><?php echo $cartContentProduct->name; ?></span>
            </div>
        <?php endforeach; ?>
    </div>
    <a href="checkout" class="btn btn-primary">Buy!</a>
</div>
