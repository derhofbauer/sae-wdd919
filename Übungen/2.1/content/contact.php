<style>
  label {
    display: block;
  }
</style>

<main>
    <form action="index.php?page=contact-validate" method="post" novalidate>
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="<?php echo $_POST['name']; ?>">
        </div>
        <div class="form-group">
            <label for="email">E-Mail</label>
            <input type="email" name="email" id="email" value="<?php echo $_POST['email']; ?>">
        </div>
        <div class="form-group">
            <label for="gender">Geschlecht</label>
            <?php
            $genders = [
                'm' => 'weiblich',
                'f' => 'männlich',
                'nb' => 'non-binary'
            ];

            foreach ($genders as $htmlValue => $label): ?>
                <label>
                    <?php
                    $checkedParticle = '';
                    if ($_POST['gender'] === $htmlValue) {
                        $checkedParticle = ' checked';
                    }
                    ?>
                    <input type="radio" name="gender" value="<?php echo $htmlValue; ?>"<?php echo $checkedParticle; ?>> <?php echo $label; ?>
                </label>
            <?php endforeach; ?>
        </div>
        <div class="form-group">
            <label for="message">Nachricht</label>
            <textarea name="message" id="message" cols="30" rows="5"><?php echo $_POST['message']; ?></textarea>
        </div>
        <div class="form-group">
            <label for="newsletter">Newsletter?</label>
            <input type="checkbox" name="newsletter" id="newsletter">
        </div>
        <button type="submit">Abschicken</button>
    </form>
</main>
