<?php require __DIR__ . '/../partials/success.php'; ?>
<?php require __DIR__ . '/../partials/errors.php'; ?>

<div class="row">

    <?php if (!empty($orders)): ?>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Products</th>
                <th>Price</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo $order->id; ?></td>
                    <td><?php echo \App\Models\Order::formatDate($order->crdate); ?></td>
                    <td>
                        <ul>
                            <?php foreach ($order->getProducts() as $product): ?>
                                <li><?php echo $product->quantity . "x " . $product->name; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                    <td><?php echo \App\Models\Product::formatPrice($order->total); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">Leider hast du noch keine Bestellungen getätigt :(</div>
    <?php endif; ?>

</div>
