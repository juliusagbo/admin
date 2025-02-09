<?php
session_start();
$host = "localhost";
$user = "root";  
$pass = "";
$dbname = "loan_management";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Redirect if not logged in
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin-login.php");
    exit();
}

// Handle loan approval/rejection
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["loan_id"])) {
    $loan_id = $_POST["loan_id"];
    $status = $_POST["status"];

    $stmt = $conn->prepare("UPDATE loans SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $loan_id);
    $stmt->execute();
    $stmt->close();
    exit();
}

// Fetch loan applications
$loans = $conn->query("SELECT id, applicant_name, amount, status FROM loans");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: white;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            text-align: center;
        }
        h1 {
            color: #f1c40f;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #1e1e1e;
        }
        th, td {
            padding: 10px;
            border: 1px solid #444;
        }
        th {
            background: #f1c40f;
            color: black;
        }
        .btn {
            padding: 8px 12px;
            margin: 5px;
            cursor: pointer;
            border: none;
            color: white;
            border-radius: 5px;
        }
        .approve { background: #28a745; }
        .reject { background: #dc3545; }
        .logout { background: #e67e22; }
        .btn:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Admin Dashboard</h1>
    <a href="admin-logout.php"><button class="btn logout">Logout</button></a>

    <table>
        <tr>
            <th>Applicant Name</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while ($loan = $loans->fetch_assoc()): ?>
        <tr data-loan-id="<?= $loan['id'] ?>">
            <td><?= htmlspecialchars($loan['applicant_name']) ?></td>
            <td>$<?= number_format($loan['amount'], 2) ?></td>
            <td class="status"><?= htmlspecialchars($loan['status']) ?></td>
            <td>
                <?php if ($loan['status'] === 'Pending'): ?>
                    <button class="btn approve" onclick="updateLoanStatus(<?= $loan['id'] ?>, 'Approved')">Approve</button>
                    <button class="btn reject" onclick="updateLoanStatus(<?= $loan['id'] ?>, 'Rejected')">Reject</button>
                <?php else: ?>
                    <?= htmlspecialchars($loan['status']) ?>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<script>
    function updateLoanStatus(loanId, status) {
        if (confirm(`Are you sure you want to ${status.toLowerCase()} this loan?`)) {
            fetch("", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `loan_id=${loanId}&status=${status}`
            }).then(() => {
                document.querySelector(`tr[data-loan-id='${loanId}'] .status`).textContent = status;
                document.querySelector(`tr[data-loan-id='${loanId}'] td:last-child`).innerHTML = status;
            });
        }
    }
</script>

</body>
</html>
<?php $conn->close(); ?>
