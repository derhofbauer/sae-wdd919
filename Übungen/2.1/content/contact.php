<style>
    label {
      display: block;
    }
</style>

<main>
    <form action="index.php?page=contact-validate" method="post" novalidate>
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name">
        </div>
        <div class="form-group">
            <label for="email">E-Mail</label>
            <input type="email" name="email" id="email">
        </div>
        <div class="form-group">
            <label for="gender">Geschlecht</label>
            <label><input type="radio" name="gender" value="f"> weiblich</label>
            <label><input type="radio" name="gender" value="m"> männlich</label>
            <label><input type="radio" name="gender" value="nb"> non-binary</label>
        </div>
        <div class="form-group">
            <label for="message">Nachricht</label>
            <textarea name="message" id="message" cols="30" rows="5"></textarea>
        </div>
        <div class="form-group">
            <label for="newsletter">Newsletter?</label>
            <input type="checkbox" name="newsletter" id="newsletter">
        </div>
        <button type="submit">Abschicken</button>
    </form>
</main>
