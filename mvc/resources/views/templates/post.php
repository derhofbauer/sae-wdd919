<?php

require_once __DIR__ . '/../partials/errors.php';
require_once __DIR__ . '/../partials/success.php';

?>

<div class="post">
    <h2>
        <?php echo $post->title; ?>

        <?php if (\App\Models\User::isLoggedIn() && \App\Models\User::getLoggedIn()->is_admin === true): ?>
            <a href="admin/posts/<?php echo $post->id; ?>/edit" class="btn btn-primary">Edit</a>
        <?php endif; ?>
    </h2>
    <div class="post__content"><?php echo $post->getContent(); ?></div>

    <div class="post__products row">
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

    <a href="blog">back ...</a>
</div>
