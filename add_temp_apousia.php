<?php
$title = '«Προσθήκη Απουσίας Εκπαιδευομένου ΣΠΗΥ»';
include('templates/header.inc.php');
check_session();
if ($_SESSION['username'] == "diaxstis") {
    $title = 'Σφάλμα πρόσβασης';
    print "<p>Δεν έχετε δικαίωμα πρόσβασης σε αυτή τη σελίδα.";
    include('templates/footer.inc.php');
} else {
?>
    <h2>Προσθήκη Απουσίας Εκπαιδευομένου ΣΠΗΥ</h2>
    <p>Προσθέστε μία απουσία σε έναν εκπαιδευόμενο</p>
    <?php
    require_once('includes/mysqli_connect.php');
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if ($id_students = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)) {
            print_add_temp_apousia_form($dbc, $id_students);
        }
    }
    if ($id_students = filter_input(INPUT_POST, 'staticID', FILTER_VALIDATE_INT)) {
        $errors = [];
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
                $query = "INSERT INTO temp_apousies (imerominia, id_students, id_aitia, id_roles, xronosfragida) VALUES (date(now()), ?, ?, ?, now())";
                $stmt = my_mysqli_prepare($dbc, $query);
                my_mysqli_stmt_bind_param($stmt, 'iii', $id_students, $id_aitia, $id_roles);
            } else {
                $query = "INSERT INTO temp_apousies (imerominia, id_students, id_aitia, tekmiriosi, id_roles, xronosfragida) VALUES (date(now()), ?, ?, ?, ?, now())";
                $stmt = my_mysqli_prepare($dbc, $query);
                my_mysqli_stmt_bind_param($stmt, 'iisi', $id_students, $id_aitia, $tekmiriosi, $id_roles);
            }
            my_mysqli_stmt_execute($stmt);
            if (my_mysqli_stmt_affected_rows($stmt) == 1) {
                print "<p class='alert alert-success'>Η απουσία του μαθητή προστέθηκε <strong>επιτυχώς!</strong></p>\n";
            } else {
                print "<p class='alert alert-warning'>Η απουσία του μαθητή <strong>ΔΕΝ</strong> προστέθηκε!</p>\n";
                print_system_error();
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
}
?>