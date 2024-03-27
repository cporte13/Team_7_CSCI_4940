<?php
session_start();

//For some reason this fixes the login issues we've been having
$username = $_POST['username'];
$password = $_POST['password'];
$_SESSION['username'] = $_POST['username'];
$_SESSION['password'] = $_POST['password'];

include 'functions.php';

if ( !isset($_POST['username'], $_POST['password'])) {
    exit('Both fields are required.');
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch();

        if ($password == $user['password']) {
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            session_regenerate_id(true);
            header('Location: tickets.php');
        } else {
            echo "username (SESSION): " . $_SESSION['username'] . "<br>\n";
            echo "password (SESSION): " . $_SESSION['password'] . "<br>\n";
            echo "username($): " . $username . "<br>\n";
            echo "password($): " . $password . "<br>\n";
            echo "Incorrect password.";
        }
    } else {
        echo "username: " . $_SESSION['username'] . "<br>\n";
        echo "password: " . $_SESSION['password'] . "<br>\n";
        echo "username($): " . $username . "<br>\n";
        echo "password($): " . $password . "<br>\n";
        echo "Failed to retrieve database.";
    }
} catch (PDOException $e) {
    $_SESSION['error'] = $e->getMessage();
}
?>