<?php
$title = '«Διαγραφή Απουσίας Μαθητή ΣΠΗΥ»';
include('templates/header.inc.php');
check_session();
if ($_SESSION['username'] == "diaxstis") {
require_once('includes/mysqli_connect.php');
?>
<h2>Διαγραφή Απουσίας Μαθητή ΣΠΗΥ</h2>
<p>Είστε σίγουροι ότι θέλετε να διαγράψτε την επιλεχθείσα απουσία;</p>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($id_apousies = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)) {
        $query = "SELECT imerominia, id_seires, seires_name, id_students, vathmoi_abbr, spec_abbr, students_surname, students_name, id_aitia, aitia, tekmiriosi, id_roles, roles, xronosfragida 
        FROM apousies NATURAL JOIN students NATURAL JOIN vathmoi NATURAL JOIN specialization NATURAL JOIN seires NATURAL JOIN aitia NATURAL JOIN roles WHERE id_apousies = ? AND apousia = 1 AND stud_deleted = 0 AND deleted = 0";
        $stmt = my_mysqli_prepare($dbc, $query);
        my_mysqli_stmt_bind_param($stmt, 'i', $id_apousies);
        my_mysqli_stmt_execute($stmt);
        my_mysqli_stmt_store_result($stmt);
        if (my_mysqli_stmt_num_rows($stmt) == 1) {
            my_mysqli_stmt_bind_result($stmt, $imerominia, $id_seires, $seires_name, $id_students, $vathmoi_abbr, $spec_abbr, $students_surname, $students_name, $id_aitia, $aitia, $tekmiriosi, $id_roles, $roles, $xronosfragida);
            my_mysqli_stmt_fetch($stmt);
            print_delete_apousia_form($dbc, $id_apousies, $imerominia, $seires_name, $id_students, $vathmoi_abbr, $spec_abbr, $students_surname, $students_name, $id_aitia, $aitia, $tekmiriosi);
        } else {
            print "<p class='alert alert-warning'>Δεν υπάρχει η απουσία του μαθητή που αναζητήσατε!</p>\n";
        }
        mysqli_stmt_close($stmt);
    }
}
if ($id_apousies = filter_input(INPUT_POST, 'staticID', FILTER_VALIDATE_INT)) {
    $query = "UPDATE apousies SET apousia = 0 WHERE id_students = ?";
    $stmt = my_mysqli_prepare($dbc, $query);
    my_mysqli_stmt_bind_param($stmt, 'i', $id_apousies);
    my_mysqli_stmt_execute($stmt);
    if (my_mysqli_stmt_affected_rows($stmt) == 1) {
        print "<p class='alert alert-success'>Η απουσία διαγράφηκε <strong>επιτυχώς!</strong></p>\n";
    } else {
        print "<p class='alert alert-warning'>Η απουσία <strong>ΔΕΝ</strong> διαγράφηκε!</p>\n";
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