<?php
require __DIR__ . '/../partials/errors.php';
require __DIR__ . '/../partials/success.php';
?>

<div class="product row">
    <?php
    /**
     * [ ] Produkt-Detailseite
     */
    ?>

    <?php
    /**
     * Gibt es Bilder für das Produkt? Wenn ja, geben wir hier alle nacheinander aus.
     */
    ?>
    <?php if (count($product->getImages()) > 0): ?>
        <!--<div class="col-6">
            <?php /*foreach ($product->getImages() as $image): */ ?>
                <img src="<?php /*echo $image */ ?>" alt="<?php /*echo $product->name */ ?>" class="img-thumbnail">
            <?php /*endforeach; */ ?>
        </div>-->
        <?php require_once __DIR__ . '/../partials/gallery.php'; ?>
    <?php endif; ?>

    <div class="col-6">
        <h2>
            <?php echo $product->name; ?>
            <?php if ($product->getAverageRating() !== null): ?>
                <small>(Rating: <?php echo $product->getAverageRating(); ?>)</small>
            <?php endif; ?>
        </h2>
        <div>Price: <strong><?php echo $product->getPrice() ?></strong></div>
        <div><?php echo $product->description; ?></div>
        <div>
            Categories:

            <ul>
                <?php
                foreach ($categories as $category) {
                    echo "<li>$category->name</li>";
                }
                ?>
            </ul>
        </div>

        <form action="cart/add/<?php echo $product->id; ?>" method="post" class="add-to-cart">
            <input type="hidden" value="<?php echo $product->id; ?>" name="product_id">

            <div class="input-group">
                <div class="input-group-prepend">
                    <button type="submit" class="btn btn-primary">Add to cart</button>
                </div>
                <input type="number" class="form-control" min="1" name="numberToAdd" value="1">
            </div>
        </form>

        <?php require_once __DIR__ . '/../partials/ratings.php'; ?>

        <div class="related-products">
            <h3>Verwandte Produkte</h3>
            <ul>
                <?php foreach ($relatedProducts as $relatedProduct): ?>
                    <li><?php echo $relatedProduct->name; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

    </div>

</div>
