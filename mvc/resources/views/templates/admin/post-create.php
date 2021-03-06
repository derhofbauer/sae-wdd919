<form action="admin/posts/create/do" method="post" enctype="multipart/form-data">

    <?php require __DIR__ . '/../../partials/success.php'; ?>
    <?php require __DIR__ . '/../../partials/errors.php'; ?>

    <div class="row">
        <div class="col">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" class="form-control" value="<?php echo \Core\Session::old('title'); ?>">
            </div>
            <div class="form-group">
                <label for="content">Content</label>
                <textarea name="content" id="content" class="form-control"><?php echo \Core\Session::old('content'); ?></textarea>
            </div>
        </div>

        <div class="col">
            <label>Products</label>

            <?php
            /**
             * Alle Produkte durchgehen, nicht nur die aus der Kategorie
             */
            foreach ($products as $product) : ?>

            <div class="form-check">
                <input type="checkbox" name="products[<?php echo $product->id; ?>]" id="products[<?php echo $product->id; ?>]" class="form-check-input">
                <label for="products[<?php echo $product->id; ?>]" class="form-check-label"><?php echo $product->name; ?></label>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="admin" class="btn btn-danger float-right">Abort</a>
        </div>
    </div>

</form>
