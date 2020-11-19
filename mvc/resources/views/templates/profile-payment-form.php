<?php require __DIR__ . '/../partials/success.php'; ?>
<?php require __DIR__ . '/../partials/errors.php'; ?>

<h2>Edit payment method</h2>
<form action="profile/payments/<?php echo $payment->id; ?>/edit/do" method="post" novalidate>
    <div class="form-group">
        <label for="name">Owner</label>
        <input type="text" name="name" id="name" placeholder="e.g. Marvin" class="form-control" value="<?php echo $payment->name; ?>" required>
    </div>
    <div class="form-group">
        <label for="number">Card number</label>
        <input type="text" name="number" id="number" class="form-control" value="<?php echo $payment->number; ?>" required>
    </div>
    <div class="row">
        <div class="form-group col">
            <label for="ccv">CCV</label>
            <input type="text" name="ccv" id="ccv" class="form-control" value="<?php echo $payment->ccv; ?>" required>
        </div>
        <div class="form-group col">
            <label for="expires">Expiration Date</label>
            <input type="text" name="expires" id="expires" class="form-control" value="<?php echo $payment->expires; ?>" required>
        </div>
    </div>

    <button type="submit" class="btn btn-primary mt-2" value="create-new" name="submit-button">Save</button>
</form>
