<form action="profile/do" method="post" enctype="multipart/form-data">

    <?php require __DIR__ . '/../partials/success.php'; ?>
    <?php require __DIR__ . '/../partials/errors.php'; ?>

    <div class="row">

        <div class="col">
            <div class="form-group">
                <label for="email">E-Mail</label>
                <input type="email" name="email" id="email" value="<?php echo $user->email; ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" value="<?php echo $user->username; ?>" class="form-control">
            </div>
        </div>

        <div class="col">
            <div class="form-group">
                <label for="firstname">Firstname</label>
                <input type="text" name="firstname" id="firstname" value="<?php echo $user->firstname; ?>" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="lastname">Lastname</label>
                <input type="text" name="lastname" id="lastname" value="<?php echo $user->lastname; ?>" class="form-control" required>
            </div>
        </div>

        <div class="col">
            <div class="form-group">
                <label for="password">Neues Passwort</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>
            <div class="form-group">
                <label for="password_repeat">Neues Passwort wiederholen</label>
                <input type="password" name="password_repeat" id="password_repeat" class="form-control">
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col">
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </div>

    <div class="row">

        <div class="col addresses">
            <h2>Addresses</h2>
            <div class="list-group">
                <?php foreach ($addresses as $address): ?>
                    <a href="profile/addresses/<?php echo $address->id; ?>/edit" class="list-group-item">
                        <small>#<?php echo $address->id; ?></small>
                        <strong><?php echo "{$address->street}  {$address->street_nr}" ?></strong>
                        <p>
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
                            ?>
                        </p>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="col payments">
            <h2>Payment Methods</h2>
            <div class="list-group">
                <?php foreach ($payments as $payment): ?>
                    <a href="profile/payments/<?php echo $payment->id; ?>/edit" class="list-group-item">
                        <small>#<?php echo $payment->id; ?></small>
                        <strong><?php echo $payment->name; ?></strong><br>
                        Number: ...<?php echo substr($payment->number, -4); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

</form>
