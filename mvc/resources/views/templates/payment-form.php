<?php require __DIR__ . '/../partials/errors.php'; ?>

<div class="products row">

    <div class="col">
        <h2>Create new payment method</h2>
        <form action="checkout/payment/do" method="post" novalidate>
            <div class="form-group">
                <label for="name">Owner</label>
                <input type="text" name="name" id="name" placeholder="e.g. Marvin" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="number">Card number</label>
                <input type="text" name="number" id="number" class="form-control" required>
            </div>
            <div class="row">
                <div class="form-group col">
                    <label for="ccv">CCV</label>
                    <input type="text" name="ccv" id="ccv" class="form-control" required>
                </div>
                <div class="form-group col">
                    <label for="expires">Expiration Date</label>
                    <input type="text" name="expires" id="expires" class="form-control" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-2" value="create-new" name="submit-button">Save & Proceed</button>
        </form>
    </div>

    <div class="col">
        <h2>Use existing payment method</h2>
        <form action="checkout/payment/do" method="post" novalidate>
            <div class="form-group">
                <label for="card">Use existing card</label>
                <select name="card" id="card" class="form-control">
                    <option value="_default">Bitte auswählen ...</option>
                    <?php foreach ($payments as $payment): ?>
                        <option value="<?php echo $payment->id; ?>"><?php echo $payment->name; ?>: ...<?php echo substr($payment->number, -4); ?> </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary mt-2" value="use-existing" name="submit-button">Save & Proceed</button>
        </form>
    </div>

</div>
