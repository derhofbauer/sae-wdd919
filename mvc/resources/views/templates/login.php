<div class="products row">

    <div class="col">
        <form action="login/do" method="post">
            <div class="form-group">
                <label for="usernameOrEmail">Username or E-Mail</label>
                <input type="text" name="usernameOrEmail" id="usernameOrEmail" placeholder="e.g. superadmin@system.com" class="form-control">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>
            <div class="form-check">
                <input type="checkbox" name="remember" id="remember" class="form-check-input">
                <label class="form-check-label" for="remember">Remember Me</label>
            </div>

            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>

</div>
