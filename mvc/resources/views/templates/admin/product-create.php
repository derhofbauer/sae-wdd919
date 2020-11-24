<form action="admin/products/create/do" method="post" enctype="multipart/form-data">

    <?php require __DIR__ . '/../../partials/success.php'; ?>
    <?php require __DIR__ . '/../../partials/errors.php'; ?>

    <div class="row">

        <div class="col">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" value="<?php echo \Core\Session::old('name'); ?>" class="form-control">
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" name="price" id="price" step="0.01" min="0" value="<?php echo \Core\Session::old('price'); ?>" class="form-control">
            </div>
        </div>

        <div class="col">
            <div class="form-group">
                <label for="stock">Stock</label>
                <input type="number" name="stock" id="stock" min="0" value="<?php echo \Core\Session::old('stock'); ?>" class="form-control">
            </div>

            <div class="form-group">
                <label for="images">Add Images</label>
                <input type="file" name="images[]" id="images" class="form-control-file" multiple>
            </div>
        </div>

    </div>

    <div class="row">

        <div class="form-group col">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control"><?php echo \Core\Session::old('description'); ?></textarea>
        </div>

        <div class="col">
            <label>Categories</label>

            <?php foreach ($categories as $category) : ?>

                <div class="form-check">
                    <input type="checkbox" name="categories[<?php echo $category->id; ?>]" id="categories[<?php echo $category->id; ?>]" class="form-check-input">
                    <label for="categories[<?php echo $category->id; ?>]" class="form-check-label"><?php echo $category->name; ?></label>
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
