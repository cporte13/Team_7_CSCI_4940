<?php
include "functions.php";

session_start();

$pdo = pdo_connect_mysql();

if(isset($_POST['submit'])) {
    $title = $_POST['title'];
    $username = $_POST['username'];
    $msg = $_POST['msg'];

    $sql = 'INSERT INTO tickets (title, username, msg) VALUES (:title, :username, :msg)';
    $stmt = $pdo->prepare($sql);

    $data = [
        ':title'=> $title,
        'username'=> $username,
        'msg' => $msg
    ];
    $stmt_execute = $stmt->execute($data);

    if ($stmt_execute) {
        echo "Inserted data successfully";
        header("Location: tickets.php");
        exit;
    } else {
        echo "Data insertion unsuccessful";
        header("Location: tickets.php");
        exit;
    }
}
?>

<?template_header('Create Ticket')?>
<link rel="stylesheet" href="css/tickets.css">

<div class="content create">
    <h2>Create A Ticket</h2>
    <form action="createticket.php" method="post">
        <label for="title">Title</label>
        <input type="text" name="title" placeholder="Title" id="title" required>
        <label for="username">Username</label>
        <input type="text" name="username" placeholder="Username" id="username" required>
        <label for="msg">Message</label>
        <textarea name="msg" placeholder="Type here..." id="msg" required></textarea>
        <input type="submit" value="Create" name="submit">
    </form>
</div>

<?template_footer()?>