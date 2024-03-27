<?php
session_start();

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
                    <th>User</th>
                    <th>Title</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
                <?php
                $query = $pdo->prepare("SELECT role FROM users WHERE username = '{$_SESSION['username']}'");
                $query->execute();
                $role = $query->fetch();

                if ($role['role'] == "admin") {
                    $stmt = $pdo->prepare("SELECT * FROM tickets ORDER BY created DESC");
                    $stmt->execute();
                } else if ($role['role'] == "user") {
                    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE username = '{$_SESSION['username']}' ORDER BY created DESC");
                    $stmt->execute();
                } else {
                    echo "Role not found! Aborting...";
                }

                $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($tickets as $ticket) {
                    echo '<tr>';
                    echo '<td>' . $ticket['id'] . '</td>';
                    echo '<td>' . $ticket['username'] . '</td>';
                    echo '<td>' . $ticket['title'] . '</td>';
                    echo '<td><a href="viewtickets.php?id='.$ticket['id'].'">' . $ticket['msg'] . '</a>' . '</td>';
                    echo '<td>' . $ticket['created'] . '</td>';

                    if ($ticket['status'] == "open") {
                        echo '<td class="status_btn_open">' . $ticket['status'] . '</td>';
                    } else if ($ticket['status'] == "closed") {
                        echo '<td class="status_btn_closed">' . $ticket['status'] . '</td>';
                    } else if ($ticket['status'] == "on hold") {
                        echo '<td class="status_btn_onhold">' . $ticket['status'] . '</td>';
                    } else if ($ticket['status'] == "resolved") {
                        echo '<td class="status_btn_resolved">' . $ticket['status'] . '</td>';
                    }

                    echo '</tr>';
                }
                ?>
            </table>
        </div>
    </div>

<?=template_footer()?>