<style>
  label {
    display: block;
  }
</style>

<?php

/**
 * Prüft ob ein Fehler für ein Feld existiert
 *
 * @param array  $errors
 * @param string $key
 *
 * @return bool
 */
function hasError (array $errors, string $key): bool
{
    return array_key_exists($key, $errors);
}

/**
 * Gibt einen Fehler aus.
 *
 * @param array  $errors
 * @param string $key
 */
function renderError (array $errors, string $key)
{
    if (hasError($errors, $key)) {
        echo "<p class=\"error\">" . $errors[$key] . "</p>";
    }
}

/**
 * Gibt eine HTML Klasse aus, wenn ein Fehler für das Feld existiert
 *
 * @param array  $errors
 * @param string $key
 */
function errorClass (array $errors, string $key)
{
    if (hasError($errors, $key)) {
        echo ' form-group--has-error';
    }
}

if (!isset($success)) {
    $errors = [];
}

?>

<main>
    <form action="index.php?page=contact-validate" method="post" novalidate>
        <div class="form-group<?php errorClass($errors, 'name'); ?>">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="<?php if (isset($_POST['name'])) {
                echo $_POST['name'];
            } ?>">
            <?php renderError($errors, 'name'); ?>
        </div>
        <div class="form-group<?php errorClass($errors, 'email'); ?>">
            <label for="email">E-Mail</label>
            <input type="email" name="email" id="email" value="<?php if (isset($_POST['email'])) {
                echo $_POST['email'];
            } ?>">
            <?php renderError($errors, 'email'); ?>
        </div>
        <div class="form-group<?php errorClass($errors, 'gender'); ?>">
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
                    if (isset($_POST['gender']) && $_POST['gender'] === $htmlValue) {
                        $checkedParticle = ' checked';
                    }
                    ?>
                    <input type="radio" name="gender" value="<?php echo $htmlValue; ?>"<?php echo $checkedParticle; ?>> <?php echo $label; ?>
                </label>
            <?php endforeach; ?>
            <?php renderError($errors, 'gender'); ?>
        </div>
        <div class="form-group<?php errorClass($errors, 'message'); ?>">
            <label for="message">Nachricht</label>
            <textarea name="message" id="message" cols="30" rows="5"><?php if (isset($_POST['message'])) {
                    echo $_POST['message'];
                } ?></textarea>
            <?php renderError($errors, 'message'); ?>
        </div>
        <div class="form-group<?php errorClass($errors, 'phone'); ?>">
            <label for="phone">Telefon</label>
            <input type="tel" name="phone" id="phone" value="<?php if (isset($_POST['phone'])) {
                echo $_POST['phone'];
            } ?>">
            <?php renderError($errors, 'phone'); ?>
        </div>
        <div class="form-group">
            <label for="newsletter">Newsletter?</label>
            <input type="checkbox" name="newsletter" id="newsletter">
        </div>
        <button type="submit">Abschicken</button>
    </form>
</main>
