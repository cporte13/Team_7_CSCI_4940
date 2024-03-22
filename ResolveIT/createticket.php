<?php
session_start();

include "functions.php";

if(isset($_POST['submit'])) {
    $title = $_POST['title'];
    $username = $_SESSION['username'];
    $msg = $_POST['msg'];

    //Prepares data and binds params to prevent SQL injection
    $stmt = $pdo->prepare('INSERT INTO tickets (title, username, msg) VALUES (:title, :username, :msg)');
    $stmt-> bindParam(':title', $title);
    $stmt-> bindParam(':username', $username);
    $stmt->bindParam(':msg', $msg);

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
        <label for="msg">Message</label>
        <textarea name="msg" placeholder="Type here..." id="msg" required></textarea>
        <input type="submit" value="Create" name="submit">
    </form>
</div>

<?template_footer()?>