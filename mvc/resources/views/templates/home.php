<?php

require_once __DIR__ . '/../partials/errors.php';
require_once __DIR__ . '/../partials/success.php';

?>

<div class="products row">
    <?php
    /**
     * [ ] Produktübersicht
     */

    foreach ($products as $product): ?>

        <div class="col-4 product">
            <?php
            /**
             * Gibt es Bilder? Wenn ja, geben wir hier das erste davon als Produktbild aus.
             */
            ?>
            <?php if (count($product->getImages()) > 0): ?>
                <img src="<?php echo $product->getImages()[0]; ?>" alt="<?php echo $product->name ?>" class="img-thumbnail">
            <?php endif; ?>
            <h2>
                <?php echo $product->name; ?>
                <?php if ($product->getAverageRating() !== null): ?>
                    <small>(Rating: <?php echo $product->getAverageRating(); ?>)</small>
                <?php endif; ?>
            </h2>
            <div><?php echo $product->description; ?></div>
            <a href="products/<?php echo $product->id; ?>">more ...</a>
        </div>

    <?php endforeach; ?>

</div>
