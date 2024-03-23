<?php
$title = '«Προσθήκη Εκπαιδευομένου ΣΠΗΥ»';
include('templates/header.inc.php');
check_session();
if ($_SESSION['username'] == "diaxstis") {

?>
<h2>Προσθήκη Εκπαιδευομένου ΣΠΗΥ</h2>
<p>Προσθέστε έναν εκπαιδευόμενο</p>
<?php
require_once('includes/mysqli_connect.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
    } else {
        $query = "INSERT INTO students (id_vathmoi, id_specialization, students_surname, students_name, id_seires, stud_deleted) VALUES (?, ?, ?, ?, ?, 0)";
        $stmt = my_mysqli_prepare($dbc, $query);
        my_mysqli_stmt_bind_param($stmt, 'iissi', $vathmos, $spec, $surname, $name, $seira);
        my_mysqli_stmt_execute($stmt);
        if (my_mysqli_stmt_affected_rows($stmt) == 1) {
            print "<p class='alert alert-success'>Ο μαθητής εισήχθη <strong>επιτυχώς!</strong></p>\n";
        } else {
            print "<p class='alert alert-warning'>Ο μαθητής <strong>ΔΕΝ</strong> εισήχθη!</p>\n";
            print_system_error();
        }
        mysqli_stmt_close($stmt);
    }
}

print_add_student_form($dbc);
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