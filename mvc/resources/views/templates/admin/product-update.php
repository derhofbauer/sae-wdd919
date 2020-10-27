<!-- @todo: comment -->
<form action="admin/products/<?php echo $product->id; ?>/edit/do" method="post" enctype="multipart/form-data">

    <div class="row">

        <div class="col">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" value="<?php echo $product->name; ?>" class="form-control">
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" name="price" id="price" step="0.01" min="0" value="<?php echo $product->price; ?>" class="form-control">
            </div>
        </div>

        <div class="col">
            <div class="form-group">
                <label for="stock">Stock</label>
                <input type="number" name="stock" id="stock" min="0" value="<?php echo $product->stock; ?>" class="form-control">
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
            <textarea name="description" id="description" class="form-control"><?php echo $product->description; ?></textarea>
        </div>

    </div>

    <div class="row">

        <?php foreach ($product->getImages() as $image): ?>
        <div class="form-group col">
            <label for="delete-image[<?php echo $image; ?>]">
                <img src="<?php echo $image ?>" width="150">
                <input type="checkbox" name="delete-image[<?php echo $image; ?>]" id="delete-image[<?php echo $image; ?>]"> Delete?
            </label>
        </div>
        <?php endforeach; ?>

    </div>

    <div class="row">
        <div class="col">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="admin" class="btn btn-danger float-right">Abort</a>
        </div>
    </div>

</form>
