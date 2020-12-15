<table class="table table-striped cart-table">
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
        <tr class="product-<?php echo $product->id; ?>">
            <td><?php echo $product->id; ?></td>
            <td><?php echo $product->name; ?></td>
            <td>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <a href="cart/remove-one/<?php echo $product->id; ?>" class="btn btn-outline-secondary ajax-cart-button ajax-cart-button-remove">-</a>
                    </div>

                    <input type="number" min="0" name="cart-quantity[<?php echo $product->id; ?>]" id="cart-quantity[<?php echo $product->id; ?>]" value="<?php echo $product->quantity; ?>" class="form-control" style="width: 80px">

                    <div class="input-group-append">
                        <a href="cart/add-one/<?php echo $product->id; ?>" class="btn btn-outline-secondary ajax-cart-button ajax-cart-button-add">+</a>
                    </div>
                </div>
            </td>
            <td class="subtotal"><?php echo \App\Models\Product::formatPrice($product->subtotal); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
    <tr>
        <td></td>
        <td></td>
        <td>Summe:</td>
        <td class="cart-total"><?php echo \App\Models\Product::formatPrice($total); ?></td>
    </tr>
    </tfoot>
</table>
