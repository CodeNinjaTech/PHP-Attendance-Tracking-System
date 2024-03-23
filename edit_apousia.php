<?php
$title = '«Τροποποίηση Απουσίας Μαθητή ΣΠΗΥ»';
include('templates/header.inc.php');
check_session();
if ($_SESSION['username'] == "diaxstis") {
require_once('includes/mysqli_connect.php');
?>
<h2>Τροποποίηση Απουσίας Μαθητή ΣΠΗΥ</h2>
<p>Τροποποιήστε την απουσία του επιλεχθέντα μαθητή ΣΠΗΥ</p>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($id_apousies = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)) {
        get_apousia($dbc, $id_apousies);
    }
}
if ($id_apousies = filter_input(INPUT_POST, 'staticID', FILTER_VALIDATE_INT)) {
    $errors = [];
    if (empty($_POST['date'])) {
        $date = NULL;
        $errors[] = 'Παρακαλώ επιλέξτε ημερομηνία';
    } else {
        $imerominia = $_POST['date'];
    }
    if (empty($_POST['aitia'])) {
        $id_aitia = NULL;
        $errors[] = 'Παρακαλώ επιλέξτε αιτία απουσίας';
    } else {
        $id_aitia = $_POST['aitia'];
    }
    if (!empty($_FILES)) {
        $file = str_replace(' ', '_', $_FILES['tekmiriosi']['name']);
        if (move_uploaded_file($_FILES['tekmiriosi']['tmp_name'], "uploads/$file")) {
            $tekmiriosi = "uploads\\" . $file;
        } else {
            $phpFileUploadErrors = array(
                0 => 'Το αρχείο ανέβηκε με επιτυχία',
                1 => 'Το αρχείο υπερβαίνει το ορισθέν μέγεθος στον φάκελο php.ini',
                2 => 'Το αρχείο υπερβαίνει το ορισθέν μέγεθος που είναι ορισμένος στην φόρμα της παρούσης σελίδας',
                3 => 'Το αρχείο ανέβηκε μερικώς',
                4 => 'Δεν ανέβηκε κανένα αρχείο',
                6 => 'Λείπει ένας προσωρινός φάκελος',
                7 => 'Αδυναμία εγγραφής αρχείου στον δίσκο',
                8 => 'Μία επέκταση PHP σταμάτησε την διαδικασία',
            );
            if ($_FILES['tekmiriosi']['error'] != 4 && $_FILES['tekmiriosi']['error'] != 0) {
                $errors[] = "Το αρχείο σας δεν μπόρεσε να ανέβει γιατί: \n" . $phpFileUploadErrors[$_FILES['tekmiriosi']['error']];
            }
        }
    }
    if (!empty($errors)) {
        print_error_messages($errors);
        get_apousia($dbc, $id_apousies);
    } else {
        $query = "SELECT id_roles FROM roles WHERE username = ?";
        $stmt = my_mysqli_prepare($dbc, $query);
        my_mysqli_stmt_bind_param($stmt, 's', $_SESSION['username']);
        my_mysqli_stmt_execute($stmt);
        my_mysqli_stmt_store_result($stmt);
        if (my_mysqli_stmt_num_rows($stmt) == 1) {
            my_mysqli_stmt_bind_result($stmt, $id_roles);
            my_mysqli_stmt_fetch($stmt);
        }
        if ($_FILES['tekmiriosi']['error'] == 4) {
            $query = "UPDATE apousies SET imerominia = ?, id_aitia = ?, tekmiriosi = NULL, id_roles = ?, xronosfragida = now() WHERE id_apousies = ? AND apousia = 1";
            if (isset($_POST['hold'])) {
                $query = "UPDATE apousies SET imerominia = ?, id_aitia = ?, id_roles = ?, xronosfragida = now() WHERE id_apousies = ? AND apousia = 1";
            }
            $stmt = my_mysqli_prepare($dbc, $query);
            my_mysqli_stmt_bind_param($stmt, 'siii', $imerominia, $id_aitia, $id_roles, $id_apousies);
        } else {
            $query = "UPDATE apousies SET imerominia = ?, id_aitia = ?, tekmiriosi = ?, id_roles = ?, xronosfragida = now() WHERE id_apousies = ? AND apousia = 1";
            $stmt = my_mysqli_prepare($dbc, $query);
            my_mysqli_stmt_bind_param($stmt, 'sisii', $imerominia, $id_aitia, $tekmiriosi, $id_roles, $id_apousies);
        }
        my_mysqli_stmt_execute($stmt);
        if (my_mysqli_stmt_affected_rows($stmt) == 1) {
            print "<p class='alert alert-success'>Τα στοιχεία της απουσίας του μαθητή τροποποιήθηκαν <strong>επιτυχώς!</strong></p>\n";
        } else {
            print "<p class='alert alert-warning'>Τα στοιχεία της απουσίας του μαθητή <strong>ΔΕΝ</strong> τροποποιήθηκαν!</p>\n";
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