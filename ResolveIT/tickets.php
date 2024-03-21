<?php
session_start();
echo session_id();

include 'functions.php';

if (!isset ($_SESSION["loggedin"])) {
    header('Location: login.php');
    exit;
}
?>

<?=template_header('Tickets')?>
    <div class="content home">
        <h2>Tickets</h2>
        <a href="createticket.php" class="create_btn">Create Ticket</a>

        <div class="tickets-list">
            <table class="tickets_table">
                <tr>
                    <th>ID</th>
                    <th><?php echo $_SESSION['username'] ?></th>
                    <th>Role</th>
                    <th>Title</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
                <?php
                $query = $pdo->prepare("SELECT role FROM users WHERE username = '{$_SESSION['username']}'");
                $query->execute();
                $role = $query->fetch();

                $stmt = $pdo->prepare("SELECT * FROM tickets WHERE username = '{$_SESSION['username']}' ORDER BY created DESC");
                $stmt->execute();
                $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($tickets as $ticket) {
                    echo '<tr>';
                    echo '<td>' . $ticket['id'] . '</td>';
                    echo '<td>' . $ticket['username'] . '</td>';
                    echo '<td>' . $role['role'] . '</td>';
                    echo '<td>' . $ticket['title'] . '</td>';
                    echo '<td><a href="viewtickets.php?id='.$ticket['id'].'">' . $ticket['msg'] . '</a>' . '</td>';
                    echo '<td>' . $ticket['created'] . '</td>';
                    echo '<td>' . $ticket['status'] . '</td>';
                    echo '</tr>';
                }
                ?>
            </table>
        </div>
    </div>

<?=template_footer()?>