<!DOCTYPE html>
<html>
    <?php include('include/header.php'); ?>

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>ResolveIT Login</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/login.css">
        <script src="https://kit.fontawesome.com/fc02c2a45c.js" crossorigin="anonymous"></script>
    </head>
    <body>
        <h1>Login</h1>
        <div class="login">
            <form action="authenticate.php" method="post">
                <div>
                <i class="fas fa-user icon"></i>
                <input type="text" class="input-field" name="username" placeholder="Username" id="username" required>
                </div>
                <div>
                <i class="fas fa-lock icon"></i>
                <input type="text" class="input-field" name="password" style="-webkit-text-security: circle"  placeholder="Password" id="password" required>
                </div>
                <input type="submit" value="Login" id="submit">
            </form>
        </div>
        <?php
        include ('include/footer.php');
        ?>
    </body>
</html>