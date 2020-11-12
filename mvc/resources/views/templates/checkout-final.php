<?php require __DIR__ . '/../partials/errors.php'; ?>

<div class="row">

    <div class="address col">
        <h2>Lieferadresse</h2>
        <div>
            <strong><?php echo \App\Models\User::getLoggedIn()->firstname . ' ' . \App\Models\User::getLoggedIn()->lastname; ?></strong><br>
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

<h2>Produktübersicher</h2>
<?php require_once __DIR__ . '/../partials/products-table.php'; ?>

<a href="cart" class="btn btn-link">Bestellung abbrechen :(</a>
<a href="checkout/finish" class="btn btn-primary">Zahlungspflichtig bestellen</a>
