<table class="table table-striped">
    <thead>
    <tr>
        <th>#</th>
        <th>Name</th>
        <th>Quantity</th>
        <th>Subtotal</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($products as $product): ?>
        <tr>
            <td><?php echo $product->id; ?></td>
            <td>
                <?php echo $product->name; ?>
                <?php if (isset($product->comment)): ?>
                    <hr>
                    <strong class="comment"><?php echo $product->comment; ?></strong>
                <?php endif; ?>
            </td>
            <td><?php echo $product->quantity; ?></td>
            <td><?php echo \App\Models\Product::formatPrice($product->subtotal); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
    <tr>
        <td></td>
        <td></td>
        <td>Summe:</td>
        <td><?php echo \App\Models\Product::formatPrice($total); ?></td>
    </tr>
    </tfoot>
</table>
