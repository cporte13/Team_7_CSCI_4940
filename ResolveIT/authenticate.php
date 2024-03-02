<?php
session_start();

//Connect to phpticket database
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = 'admin';
$DATABASE_NAME = 'phpticket';

//Connects to the database. If there is an error, outputs error.
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()){
    exit('Failed to connect to server: ' . mysqli_connect_error());
}

//Makes username and password fields required before the form is submitted
if ( !isset($_POST['username'], $_POST['password'])) {
    exit('Both fields are required.');
}

//Prepares SQL, binds parameters, then stores the result to check if the account exists
if ($stmt = $con->prepare('SELECT id, password FROM users WHERE username = ?')) {
    $stmt->bind_param('s', $_POST['username']);
    $stmt->execute();
    $stmt->store_result();

    //Authenticates user
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $password);
        $stmt->fetch();

    //Password verification
    if ($_POST['password'] === $password) {
        //Creates session for user
        session_regenerate_id();
        $_SESSION['loggedin'] = true;
        $_SESSION['name'] = $username;
        $_SESSION['id'] = $id;
        echo 'Successfully logged in!';
    } else {
        echo 'Incorrect username or password';
    }
    } else {
        echo 'Incorrect username or password';
    }
   $stmt->close();
}
?>
