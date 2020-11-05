<form action="admin/users/<?php echo $user->id; ?>/edit/do" method="post" enctype="multipart/form-data">

    <?php require __DIR__ . '/../../partials/success.php'; ?>
    <?php require __DIR__ . '/../../partials/errors.php'; ?>

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
            <div class="form-check">
                <input type="checkbox" name="is_admin" id="is_admin" class="form-check-input"<?php echo ($user->is_admin ? ' checked' : ''); ?>>
                <label for="is_admin" class="form-check-label">Is Admin?</label>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="admin" class="btn btn-danger float-right">Abort</a>
            <a href="admin/users/<?php echo $user->id; ?>/delete" class="btn btn-danger">DELETE THIS USER!</a>
        </div>
    </div>

</form>
