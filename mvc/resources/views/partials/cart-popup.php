<div class="cart-popup">
    <!--Name, Bild, Checkout-Button, Anzahl-->
    <div class="popup-content">
        <?php
        [$cartContent, $total] = \App\Controllers\CartController::getCartContent();
        foreach ($cartContent as $product): ?>
            <div class="popup-item">
                <?php if (count($product->getImages()) > 0): ?>
                    <img src="<?php echo $product->getImages()[0]; ?>" alt="<?php echo $product->name ?>" class="img-thumbnail">
                <?php endif; ?>
                <span class="quantity"><?php echo $product->quantity; ?>x</span>
                <span class="name"><?php echo $product->name; ?></span>
            </div>
        <?php endforeach; ?>
    </div>
    <a href="checkout" class="btn btn-primary">Buy!</a>
</div>
