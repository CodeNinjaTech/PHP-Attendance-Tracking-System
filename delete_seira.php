<?php
$title = '«Διαγραφή Εκπαιδευτικής Σειράς ΣΠΗΥ»';
include('templates/header.inc.php');
check_session();
if ($_SESSION['username'] == "diaxstis") {
require_once('includes/mysqli_connect.php');
?>
<h2>Διαγραφή Εκπαιδευτικής Σειράς ΣΠΗΥ</h2>
<p>Είστε σίγουροι ότι θέλετε να διαγράψτε την επιλεχθείσα Εκπαιδευτική Σειρά;</p>
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
            print_delete_seira_form($id, $seira);
        } else {
            print "<p class='alert alert-warning'>Δεν υπάρχει η εκπαιδευτική σειρά που αναζητήσατε!</p>\n";
        }
        mysqli_stmt_close($stmt);
    }
}
if ($id = filter_input(INPUT_POST, 'staticID', FILTER_VALIDATE_INT)) {
    $query = "UPDATE seires SET deleted = 1 WHERE id_seires = ?";
    $stmt = my_mysqli_prepare($dbc, $query);
    my_mysqli_stmt_bind_param($stmt, 'i', $id);
    my_mysqli_stmt_execute($stmt);
    if (my_mysqli_stmt_affected_rows($stmt) == 1) {
        print "<p class='alert alert-success'>Η εκπαιδευτική σειρά διαγράφηκε <strong>επιτυχώς!</strong></p>\n";
    } else {
        print "<p class='alert alert-warning'>Η εκπαιδευτική σειρά <strong>ΔΕΝ</strong> διαγράφηκε!</p>\n";
    }
    mysqli_stmt_close($stmt);
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