<?php require __DIR__ . '/../partials/errors.php'; ?>

<div class="products row">

    <div class="col">
        <h2>Create new address</h2>
        <form action="checkout/address/do" method="post" novalidate>
            <div class="row">
                <div class="form-group col">
                    <label for="country">Country</label>
                    <select name="country" id="country" class="form-control">
                        <option value="_default">Bitte auswählen ...</option>
                        <?php foreach ($countries as $country): ?>
                            <option value="<?php echo $country['alpha2']; ?>"><? echo $country['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col">
                    <label for="city">City</label>
                    <input type="text" name="city" id="city" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="form-group col">
                    <label for="zip">ZIP</label>
                    <input type="text" name="zip" id="zip" class="form-control" required>
                </div>
                <div class="form-group col-6">
                    <label for="street">Street</label>
                    <input type="text" name="street" id="street" class="form-control" required>
                </div>
                <div class="form-group col">
                    <label for="street_nr">Number</label>
                    <input type="text" name="street_nr" id="street_nr" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label for="extra">Additional address line</label>
                <input type="text" name="extra" id="extra" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary mt-2" value="create-new" name="submit-button">Save & Proceed</button>
        </form>
    </div>

    <div class="col">
        <h2>Use existing address</h2>
        <form action="checkout/address/do" method="post" novalidate>
            <div class="form-group">
                <label for="address">Use existing address</label>
                <select name="address" id="address" class="form-control">
                    <option value="_default">Bitte auswählen ...</option>
                    <?php foreach ($addresses as $address): ?>
                        <option value="<?php echo $address->id; ?>"><?php echo $address->street . ' ' . $address->street_nr . ', ' . $address->zip . ' ' . $address->city; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary mt-2" value="use-existing" name="submit-button">Save & Proceed</button>
        </form>
    </div>

</div>
