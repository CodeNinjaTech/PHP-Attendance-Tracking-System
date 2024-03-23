<?php
$title = '«Τροποποίηση Εκπαιδευτικής Σειράς ΣΠΗΥ»';
include('templates/header.inc.php');
check_session();
if ($_SESSION['username'] == "diaxstis") {
require_once('includes/mysqli_connect.php');
?>
<h2>Τροποποίηση Εκπαιδευτικής Σειράς ΣΠΗΥ</h2>
<p>Τροποποιήστε την επιλεχθείσα Εκπαιδευτική Σειρά</p>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)) {
        $query = "SELECT seires_name FROM seires WHERE id_seires = ? AND deleted = 0";
        $stmt = my_mysqli_prepare($dbc, $query);
        my_mysqli_stmt_bind_param($stmt, 'i', $id);
        my_mysqli_stmt_execute($stmt);
        my_mysqli_stmt_store_result($stmt);
        if (my_mysqli_stmt_num_rows($stmt) == 1) {
            my_mysqli_stmt_bind_result($stmt, $seira);
            my_mysqli_stmt_fetch($stmt);
            print_edit_seira_form($id, $seira);
        } else {
            print "<p class='alert alert-warning'>Δεν υπάρχει η εκπαιδευτική σειρά που αναζητήσατε!</p>\n";
        }
        mysqli_stmt_close($stmt);
    }
}

if ($id = filter_input(INPUT_POST, 'staticID', FILTER_VALIDATE_INT)) {
    $errors = [];
    if (!($seira = filter_input(INPUT_POST, 'seira', FILTER_SANITIZE_NUMBER_INT))) {
        $seira = NULL;
        $errors[] = 'Παρακαλώ εισάγετε εκπαιδευτική σειρά';
    }

    if (!empty($errors)) {
        print_error_messages($errors);
        print_edit_seira_form($id, $seira);
    } else {
        $query = "UPDATE seires SET seires_name = ? WHERE id_seires = ? AND deleted = 0";
        $stmt = my_mysqli_prepare($dbc, $query);
        my_mysqli_stmt_bind_param($stmt, 'ii', $seira, $id);
        my_mysqli_stmt_execute($stmt);
        if (my_mysqli_stmt_affected_rows($stmt) == 1) {
            print "<p class='alert alert-success'>Η εκπαιδευτική σειρά τροποποιήθηκε <strong>επιτυχώς!</strong></p>\n";
        } else {
            print "<p class='alert alert-warning'>Η εκπαιδευτική σειρά <strong>ΔΕΝ</strong> τροποποιήθηκε!</p>\n";
        }
        mysqli_stmt_close($stmt);
    }    
}

mysqli_close($dbc);
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