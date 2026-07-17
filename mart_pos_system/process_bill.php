<?php
// process_bill.php
require_once 'db.php';

if (isset($_POST['add_expense'])) {
    $expense_title = mysqli_real_escape_string($conn, trim($_POST['expense_title']));
    $category      = mysqli_real_escape_string($conn, $_POST['expense_category']);
    $amount        = floatval($_POST['amount']);
    $payment_type  = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $expense_date  = date("Y-m-d H:i:s");

    if (empty($expense_title) || $amount <= 0) {
        header("Location: index.php?page=bills&status=invalid_input");
        exit();
    }

    // Insert expense record into the expenses log table map
    $sql = "INSERT INTO expenses (expense_title, category, amount, payment_method, expense_date) 
            VALUES ('$expense_title', '$category', $amount, '$payment_type', '$expense_date')";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?page=bills&status=success");
        exit();
    } else {
        header("Location: index.php?page=bills&status=error");
        exit();
    }
}
