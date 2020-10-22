<div class="products row">

    <div class="col">

        <?php require __DIR__ . '/../partials/errors.php'; ?>

        <form action="sign-up/do" method="post" novalidate>
            <div class="row">
                <div class="form-group col">
                    <label for="firstname">Firstname</label>
                    <input type="text" name="firstname" id="firstname" placeholder="e.g. Marvin" class="form-control" required>
                </div>
                <div class="form-group col">
                    <label for="lastname">Lastname</label>
                    <input type="text" name="lastname" id="lastname" placeholder="e.g. the sad robot" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="form-group col">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" placeholder="e.g. marvin42" class="form-control">
                </div>
                <div class="form-group col">
                    <label for="email">E-Mail</label>
                    <input type="text" name="email" id="email" placeholder="e.g. superadmin@system.com" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="form-group col">
                    <label for="password">Passwort</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                    <small class="form-text text-muted">Muss mindestens 8 Zeichen lang sein und Zahlen, Groß- und Kleinbuchstaben und Sonderzeichen enthalten.</small>
                </div>
                <div class="form-group col">
                    <label for="password_repeat">Passwort wiederholen</label>
                    <input type="password" name="password_repeat" id="password_repeat" class="form-control" required>
                </div>
            </div>

            <div class="form-check">
                <input type="checkbox" name="agb" id="agb" class="form-check-input" required>
                <label for="agb" class="form-check-label">Ich habe die AGB gelesen, verstanden und akzeptiere
                                                          sie.</label>
            </div>

            <button type="submit" class="btn btn-primary mt-2">Login</button>
        </form>
    </div>

</div>
