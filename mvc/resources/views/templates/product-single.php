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
        <div class="col-6">
            <?php foreach ($product->getImages() as $image): ?>
                <img src="<?php echo $image ?>" alt="<?php echo $product->name ?>" class="img-thumbnail">
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="col-6">
        <h2><?php echo $product->name; ?></h2>
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

        <form action="cart/add/<?php echo $product->id; ?>" method="post">
            <div class="input-group">
                <div class="input-group-prepend">
                    <button type="submit" class="btn btn-primary">Add to cart</button>
                </div>
                <input type="number" class="form-control" min="1" name="numberToAdd" value="1">
            </div>
        </form>

    </div>

</div>
