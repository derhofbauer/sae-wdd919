<form action="admin/posts/<?php echo $post->id; ?>/edit/do" method="post" enctype="multipart/form-data">

    <?php require __DIR__ . '/../../partials/success.php'; ?>
    <?php require __DIR__ . '/../../partials/errors.php'; ?>

    <div class="row">
        <div class="col">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" class="form-control" value="<?php echo $post->title; ?>">
            </div>
            <div class="form-group">
                <label for="content">Content</label>
                <textarea name="content" id="content" class="form-control"><?php echo $post->content ?></textarea>
            </div>
        </div>

        <div class="col">
            <label>Products</label>

            <?php
            /**
             * Alle Produkte durchgehen, nicht nur die aus der Kategorie
             */
            foreach ($allProducts as $product) {
                /**
                 * Partikel vorbereiten
                 */
                $checkedParticle = '';

                /**
                 * Ist ein Produkt aus $allProducts auch in den Produkten der Kartegorie ($categoryProducts) vorhanden,
                 * bedeutet das für uns, dass die Checkbox vorausgewählt sein muss.
                 */
                if (in_array($product, $postProducts)) {
                    $checkedParticle = ' checked';
                }

                ?>

                <div class="form-check">
                    <input type="checkbox" name="products[<?php echo $product->id; ?>]" id="products[<?php echo $product->id; ?>]"<?php echo $checkedParticle; ?> class="form-check-input">
                    <label for="products[<?php echo $product->id; ?>]" class="form-check-label"><?php echo $product->name; ?></label>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="admin" class="btn btn-danger float-right">Abort</a>
<!--            <a href="admin/products/--><?php //echo $product->id; ?><!--/delete" class="btn btn-danger">DELETE THIS-->
<!--                                                                                               PRODUCT!</a>-->
        </div>
    </div>

</form>
