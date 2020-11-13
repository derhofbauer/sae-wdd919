<form action="admin/orders/<?php echo $order->id; ?>/edit/do" method="post">

    <?php require __DIR__ . '/../../partials/success.php'; ?>
    <?php require __DIR__ . '/../../partials/errors.php'; ?>

    <div class="row">

        <div class="address col">
            <h2>Lieferadresse</h2>
            <div>
                <strong><?php echo "{$user->firstname} {$user->lastname}"; ?></strong><br>
                <?php echo "{$address->street}  {$address->street_nr}" ?><br>
                <?php
                if (!empty($address->extra)) {
                    echo "$address->extra<br>";
                }
                ?>
                <?php echo "{$address->zip}  {$address->city}" ?><br>
                <?php
                $countryArray = \Core\Helpers\StaticData::getCountryFromAlpha2($address->country);
                $firstCountry = array_shift($countryArray);
                echo $firstCountry['name'];
                ?><br>
            </div>
        </div>

        <div class="payment col">
            <h2>Zahlungsmittel</h2>
            <div>
                <strong><?php echo $payment->name; ?></strong><br>
                Number: ...<?php echo substr($payment->number, -4); ?>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col">
            <?php require_once __DIR__ . '/../../partials/products-table-no-edit.php'; ?>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control">
                    <?php
                    $stati = \App\Controllers\OrderController::STATI;

                    foreach ($stati as $htmlValue => $label) {
                        $checkedParticle = '';
                        if ($htmlValue === $order->status) {
                            $checkedParticle = ' selected';
                        }

                        echo "<option value=\"$htmlValue\"$checkedParticle>$label</option>";
                    }
                    ?>
                </select>
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
