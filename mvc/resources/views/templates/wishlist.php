<?php

require_once __DIR__ . '/../partials/errors.php';
require_once __DIR__ . '/../partials/success.php';

?>

<div class="wishlist">
    <ul>
        <?php foreach ($products as $product): ?>

            <li>
                <?php echo $product->name; ?>  -
                <a href="wishlist/remove/<?php echo $product->id; ?>" class="btn btn-primary btn-sm">Remove</a>
            </li>

        <?php endforeach; ?>
    </ul>
</div>
