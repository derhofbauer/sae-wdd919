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

<div class="row">

    <div class="col orders">
        <h2>Orders</h2>
        <ul class="list-group">
            <?php foreach ($orders as $order): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="admin/orders/<?php echo $order->id; ?>/edit">
                        <?php echo "Order #{$order->id}"; ?>
                    </a>
                    <span class="badge badge-primary badge-pill"><?php echo $order->status; ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="col categories">
        <h2>Categories
            <a href="admin/categories/create" class="btn btn-primary btn-sm">Add</a>
        </h2>
        <ul class="list-group">
            <!-- Hier gehen wir alle Produkte durch und geben sie in einer Liste aus. -->
            <?php foreach ($categories as $category): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="admin/categories/<?php echo $category->id; ?>/edit">
                        <?php echo $category->name; ?>
                    </a>
                    <span class="badge badge-primary badge-pill"><?php echo $category->numberOfProducts; ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

</div>

<div class="row">
    <div class="col posts">
        <h2>Posts
            <a href="admin/posts/create" class="btn btn-primary btn-sm">Add</a>
        </h2>
        <ul class="list-group">
            <!-- Hier gehen wir alle Posts durch und geben sie in einer Liste aus. -->
            <?php foreach ($posts as $post): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="admin/posts/<?php echo $post->id; ?>/edit">
                        <?php echo $post->title; ?>
                    </a>
                    <!--<span class="badge badge-primary badge-pill"><?php /*echo $post->numberOfProducts; */?></span>-->
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
