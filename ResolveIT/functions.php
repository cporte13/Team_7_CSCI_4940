<?php
function pdo_connect_mysql() {
    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'root';
    $DATABASE_PASS = 'admin';
    $DATABASE_NAME = 'phpticket';
    try {
        return new PDO('mysql:host=' . $DATABASE_HOST . ';dbname=' . $DATABASE_NAME . ';charset=utf8', $DATABASE_USER, $DATABASE_PASS);
    } catch (PDOException $e) {
        exit('Failed to connect to database.');
    }
}

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
                    <a href="tickets.php">Tickets</a>
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