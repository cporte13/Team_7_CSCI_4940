<?php
    include('include/header.php');
    /*
    The code below currently does not work. 2/24/24
    include ('include/connectdb.php');

    $pdo = pdo_connect_mysql();

    $stmt = $pdo->prepare('SELECT * FROM tickets ORDER BY created DESC');
    $stmt->execute();
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    */
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>ResolveIT Homepage</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/styles.css">
    </head>
    <body>
        <div class="content">
            <h1>Welcome to ResolveIT</h1>
                <div class="tagline">
                    <h3>Where simple problems have simple solutions.</h3>
                </div>
                <section class="introduction">
                    <p>Here at ResolveIT, we value simplicity and effectiveness. Not everything needs to be some <br>
                        complex operation just to get a question answered. Nor should you have to wait <br> until one of
                        our technicians can tell you to turn your device off and on again. With <br> our simple-yet-effective
                        approach, we want you to be served <br>as cleanly and quickly as possible.
                    </p>
                </section>
        </div>
    </body>
    <?php
        include ('include/footer.php');
    ?>
</html>