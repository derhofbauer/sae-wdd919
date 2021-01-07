<form action="contact/send" method="post">

    <?php require_once __DIR__ . '/../partials/errors.php'; ?>
    <?php require_once __DIR__ . '/../partials/success.php'; ?>

    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" name="name" id="name" placeholder="Name" class="form-control" required value="<?php echo \Core\Session::old('name'); ?>">
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="Email" class="form-control" required value="<?php echo \Core\Session::old('email'); ?>">
    </div>

    <div class="form-group">
        <label for="message">Message</label>
        <textarea name="message" id="message" class="form-control" required><?php echo \Core\Session::old('message'); ?></textarea>
    </div>

    <button class="btn btn-primary" type="submit">Send</button>

</form>
