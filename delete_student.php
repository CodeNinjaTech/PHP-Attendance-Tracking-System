<?php
$title = '«Διαγραφή Μαθητή ΣΠΗΥ»';
include('templates/header.inc.php');
check_session();
if ($_SESSION['username'] == "diaxstis") {
require_once('includes/mysqli_connect.php');
?>
<h2>Διαγραφή Μαθητή ΣΠΗΥ</h2>
<p>Είστε σίγουροι ότι θέλετε να διαγράψτε τον επιλεχθέντα μαθητή;</p>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($studID = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)) {
        $query = "SELECT vathmoi_abbr, spec_abbr, students_surname, students_name, seires_name FROM (vathmoi NATURAL JOIN students NATURAL JOIN specialization) NATURAL JOIN seires WHERE id_students = ? AND stud_deleted = 0";
        $stmt = my_mysqli_prepare($dbc, $query);
        my_mysqli_stmt_bind_param($stmt, 'i', $studID);
        my_mysqli_stmt_execute($stmt);
        my_mysqli_stmt_store_result($stmt);
        if (my_mysqli_stmt_num_rows($stmt) == 1) {
            my_mysqli_stmt_bind_result($stmt, $vathmoi_abbr, $spec_abbr, $surname, $name, $seires_name);
            my_mysqli_stmt_fetch($stmt);
            print_delete_student_form($studID, $vathmoi_abbr, $spec_abbr, $surname, $name, $seires_name);
        } else {
            print "<p class='alert alert-warning'>Δεν υπάρχει ο μαθητής που αναζητήσατε!</p>\n";
        }
        mysqli_stmt_close($stmt);
    }
}
if ($studID = filter_input(INPUT_POST, 'staticID', FILTER_VALIDATE_INT)) {
    $query = "UPDATE students SET stud_deleted = 1 WHERE id_students = ?";
    $stmt = my_mysqli_prepare($dbc, $query);
    my_mysqli_stmt_bind_param($stmt, 'i', $studID);
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