<?php require __DIR__ . '/../../partials/success.php'; ?>
<?php require __DIR__ . '/../../partials/errors.php'; ?>

<div class="row">

    <div class="col products">
        <h2>Products
            <a href="admin/products/create" class="btn btn-primary btn-sm">Add</a>
        </h2>
        <ul class="list-group">
            <!-- Hier gehen wir alle Produkte durch und geben sie in einer Liste aus. -->
            <?php foreach ($products as $product): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center<?php echo ($product->stock < 10) ? ' list-group-item-danger' : ''; ?>">
                    <a href="admin/products/<?php echo $product->id; ?>/edit">
                        <?php echo $product->name; ?>
                    </a>
                    <span class="badge badge-primary badge-pill"><?php echo $product->stock; ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="col users">
        <h2>Users
            <!--            <a href="admin/products/create" class="btn btn-primary btn-sm">Add</a>-->
        </h2>
        <ul class="list-group">
            <!-- Hier gehen wir alle User durch und geben sie in einer Liste aus. -->
            <?php foreach ($users as $user): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="admin/users/<?php echo $user->id; ?>/edit">
                        <?php echo "{$user->lastname} {$user->firstname}"; ?>
                    </a>
                    <?php if ($user->is_admin): ?>
                        <span class="badge badge-primary badge-pill">admin</span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

</div>
