<div class="products row">
    <?php
    /**
     * [ ] Produktübersicht
     */

    foreach ($products as $product): ?>

    <div class="col-4 product">
        <?php echo $product['name']; ?>
        <?php echo $product['description']; ?>
    </div>

    <?php endforeach; ?>

</div>
