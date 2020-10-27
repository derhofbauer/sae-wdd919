<div class="row">

    <div class="col products">
        <ul class="list-group">
            <!-- @todo: comment -->
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

</div>
