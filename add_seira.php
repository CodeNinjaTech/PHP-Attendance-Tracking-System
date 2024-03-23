<?php
$title = '«Προσθήκη Εκπαιδευτικής Σειράς ΣΠΗΥ»';
include('templates/header.inc.php');
check_session();
if ($_SESSION['username'] == "diaxstis") {
?>
<h2>Προσθήκη Εκπαιδευτικής Σειράς ΣΠΗΥ</h2>
<p>Προσθέστε μία Εκπαιδευτική Σειρά</p>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    if (!($seira = filter_input(INPUT_POST, 'seira', FILTER_SANITIZE_NUMBER_INT))) {
        $seira = NULL;
        $errors[] = 'Παρακαλώ εισάγετε εκπαιδευτική σειρά ως ακέραιο';
    }
    if (!empty($errors)) {
        print_error_messages($errors);
    } else {
        require_once('includes/mysqli_connect.php');
        $query = "INSERT INTO seires (seires_name, deleted) VALUES (?, 0)";
        $stmt = my_mysqli_prepare($dbc, $query);
        my_mysqli_stmt_bind_param($stmt, 'i', $seira);
        my_mysqli_stmt_execute($stmt);
        if (my_mysqli_stmt_affected_rows($stmt) == 1) {
            print "<p class='alert alert-success'>Η εκπαιδευτική σειρά εισήχθη <strong>επιτυχώς!</strong></p>\n";
        } else {
            print "<p class='alert alert-warning'>Η εκπαιδευτική σειρά <strong>ΔΕΝ</strong> εισήχθη!</p>\n";
            print_system_error();
        }
        mysqli_stmt_close($stmt);
        mysqli_close($dbc);
    }
}

print_add_seira_form();
include('templates/footer.inc.php');
?>
</body>

</html>
<?php
ob_end_flush(); // Αποστολή του buffer στον browser και απενεργοποίηση output buffering
} else {
    $title = 'Σφάλμα πρόσβασης';
    print "<p>Δεν έχετε δικαίωμα πρόσβασης σε αυτή τη σελίδα.";
    include('templates/footer.inc.php');
}
?>