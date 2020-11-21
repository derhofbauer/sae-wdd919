<?php require __DIR__ . '/../partials/success.php'; ?>
<?php require __DIR__ . '/../partials/errors.php'; ?>

<h2>Edit address</h2>
<form action="profile/addresses/<?php echo $address->id; ?>/edit/do" method="post" novalidate>
    <div class="row">
        <div class="form-group col">
            <label for="country">Country</label>
            <select name="country" id="country" class="form-control">
                <option value="_default">Bitte auswählen ...</option>
                <?php foreach ($countries as $country): ?>
                    <?php
                    /**
                     * Damit wir hier den in der Datenbank gespeicherten Wert vorauswählen können, müssen wir die Liste
                     * dynamisch generieren und für jedes Element prüfen, ob es mit dem Wert aus der Datenbank
                     * übereinstimmt.
                     *
                     * Wir bereiten uns also einen Partikel vor, der nur in einem einzigen Fall kein leerer String ist.
                     */
                    $selectedParticle = '';

                    /**
                     * Stimmt der Wert der aktuellen Option mit dem Wert in der Datenbank überein, setzen wir den
                     * vorbereiteten Partikel. Wir verwenden die strtolower() Funktion um die Werte aus der Datenbank in
                     * Kleinbuchstaben umzuwandeln, falls sie in Großbuchstaben gespeichert worden sein sollten.
                     */
                    if ($country['alpha2'] === strtolower($address->country)) {
                        $selectedParticle = ' selected';
                    }
                    ?>
                    <option value="<?php echo $country['alpha2']; ?>"<?php echo $selectedParticle; ?>><? echo $country['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group col">
            <label for="city">City</label>
            <input type="text" name="city" id="city" class="form-control" value="<?php echo $address->city; ?>" required>
        </div>
    </div>
    <div class="row">
        <div class="form-group col">
            <label for="zip">ZIP</label>
            <input type="text" name="zip" id="zip" class="form-control" value="<?php echo $address->zip; ?>" required>
        </div>
        <div class="form-group col-6">
            <label for="street">Street</label>
            <input type="text" name="street" id="street" class="form-control" value="<?php echo $address->street; ?>" required>
        </div>
        <div class="form-group col">
            <label for="street_nr">Number</label>
            <input type="text" name="street_nr" id="street_nr" class="form-control" value="<?php echo $address->street_nr; ?>" required>
        </div>
    </div>
    <div class="form-group">
        <label for="extra">Additional address line</label>
        <input type="text" name="extra" id="extra" class="form-control" value="<?php echo $address->extra; ?>">
    </div>

    <button type="submit" class="btn btn-primary mt-2" value="create-new" name="submit-button">Save</button>
</form>
