<form action="admin/categories/create/do" method="post" enctype="multipart/form-data">

    <?php require __DIR__ . '/../../partials/success.php'; ?>
    <?php require __DIR__ . '/../../partials/errors.php'; ?>

    <div class="row">
        <div class="col">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="admin" class="btn btn-danger float-right">Abort</a>
        </div>
    </div>

</form>
