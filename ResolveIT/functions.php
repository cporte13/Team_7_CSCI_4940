<?php
//Connects to database
    $host = 'localhost';
    $user = 'root';
    $pass = 'admin';
    $dbname = 'phpticket';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_FOUND_ROWS => true,
        PDO::ATTR_PERSISTENT => true,
    ];
    $pdo = new PDO($dsn, $user, $pass, $options);


function template_header($title) {
    echo <<<EOT
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8">
            <title>$title</title>
            <link rel="stylesheet" href="css/tickets.css">
            <script src="https://kit.fontawesome.com/fc02c2a45c.js" crossorigin="anonymous"></script>
        </head>
        <body>
            <nav class="navtop">
                <div>
                    <a>ResolveIT</a>
                    <a href="tickets.php">My Tickets</a>
                    <a href="logout.php">Logout</a>
                </div>
            </nav>
    EOT;
}

function template_footer() {
    echo <<<EOT
        </body>
    </html>
    EOT;
}
?>