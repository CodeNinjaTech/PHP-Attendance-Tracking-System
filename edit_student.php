<?php
$title = '«Τροποποίηση Μαθητή ΣΠΗΥ»';
include('templates/header.inc.php');
check_session();
if ($_SESSION['username'] == "diaxstis") {
require_once('includes/mysqli_connect.php');
?>
<h2>Τροποποίηση Μαθητή ΣΠΗΥ</h2>
<p>Τροποποιήστε τον επιλεχθέντα μαθητή ΣΠΗΥ</p>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($studID = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)) {
        get_student($dbc, $studID);
    }
}
if ($studID = filter_input(INPUT_POST, 'staticID', FILTER_VALIDATE_INT)) {
    $errors = [];
    if (empty($_POST['grade'])) {
        $vathmos = NULL;
        $errors[] = 'Παρακαλώ επιλέξτε βαθμό';
    } else {
        $vathmos = $_POST['grade'];
    }
    if (empty($_POST['spec'])) {
        $spec = NULL;
        $errors[] = 'Παρακαλώ επιλέξτε ειδικότητα';
    } else {
        $spec = $_POST['spec'];
    }
    if (!($surname = filter_input(INPUT_POST, 'surname', FILTER_SANITIZE_STRING))) {
        $surname = NULL;
        $errors[] = 'Παρακαλώ εισάγετε επώνυμο';
    }
    if (!($name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING))) {
        $name = NULL;
        $errors[] = 'Παρακαλώ εισάγετε όνομα';
    }
    if (empty($_POST['seira'])) {
        $seira = NULL;
        $errors[] = 'Παρακαλώ επιλέξτε σειρά';
    } else {
        $seira = $_POST['seira'];
    }
    if (!empty($errors)) {
        print_error_messages($errors);
        get_student($dbc, $studID);
    } else {
        $query = "UPDATE students SET id_vathmoi = ?, id_specialization = ?, students_surname = ?, students_name = ?, id_seires = ? WHERE id_students = ? AND stud_deleted = 0";
        $stmt = my_mysqli_prepare($dbc, $query);
        my_mysqli_stmt_bind_param($stmt, 'iissii', $vathmos, $spec, $surname, $name, $seira, $studID);
        my_mysqli_stmt_execute($stmt);
        if (my_mysqli_stmt_affected_rows($stmt) == 1) {
            print "<p class='alert alert-success'>Τα στοιχεία του μαθητή τροποποιήθηκαν <strong>επιτυχώς!</strong></p>\n";
        } else {
            print "<p class='alert alert-warning'>Τα στοιχεία του μαθητή <strong>ΔΕΝ</strong> τροποποιήθηκαν!</p>\n";
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