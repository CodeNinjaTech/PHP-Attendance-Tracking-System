<?php

function is_loggedin()
{
    if (
        isset($_SESSION['username']) && isset($_SESSION['agent']) &&
        $_SESSION['agent'] == sha1($_SERVER['HTTP_USER_AGENT'])
    ) {
        return true;
    } else {
        return false;
    }
}

function is_administrator ()
{
    if ($_SESSION['username'] == "diaxstis") {
        return true;
    } else {
        return false;
    }
}

function check_session()
{
    if (!is_loggedin()) {
        header("Location: error.php");
        exit();
    }
}

function print_system_error()
{
    print "<h1>Σφάλμα Συστήματος</h1>\n";
    print "<p class='alert alert-warning'>Δεν ήταν δυνατή η ενέργεια αυτή λόγω σφάλματος συστήματος.
    Εργαζόμαστε για την αποκατάστασή του!</p>\n";
}

function print_error_messages($errors)
{
    if (!empty($errors)) {
        print "<p class='alert alert-warning'>Εντοπίστηκε πρόβλημα!\n";
        print "<ul class='alert alert-warning'>\n";
        foreach ($errors as $message) {
            print "<li> - $message!</li>\n";
        }
        print "</ul></p>\n";
    }
}


function my_mysqli_prepare($dbc, $q)
{
    if (!$stmt = mysqli_prepare($dbc, $q)) {
        print_system_error();
        //debugging if necessary
        die('mysqli_prepare() failed: ' . mysqli_error($dbc));
    }
    return $stmt;
}

function my_mysqli_stmt_bind_param($stmt, $type, ...$params)
{
    if (!$r = mysqli_stmt_bind_param($stmt, $type, ...$params)) {
        print_system_error();
        die('mysqli_stmt_bind_param() failed');
    }
    return $r;
}



function my_mysqli_stmt_bind_result($stmt, &...$results)
{
    if (!$r = mysqli_stmt_bind_result($stmt, ...$results)) {
        print_system_error();
        die('mysqli_stmt_bind_result() failed');
    }
    return $r;
}

function my_mysqli_stmt_execute($stmt)
{
    if (!$r = mysqli_stmt_execute($stmt)) {
        print_system_error();
        die('mysqli_stmt_execute() failed: ' . mysqli_stmt_error($stmt));
    }
    return $r;
}

function my_mysqli_stmt_store_result($stmt)
{
    if (!$r = mysqli_stmt_store_result($stmt)) {
        print_system_error();
        die('mysqli_stmt_store_result() failed: ' . mysqli_stmt_error($stmt));
    }
    return $r;
}

function my_mysqli_stmt_fetch($stmt)
{
    $r = mysqli_stmt_fetch($stmt);
    if ($r === false) {
        print_system_error();
        die('mysqli_stmt_fetch() failed: ' . mysqli_stmt_error($stmt));
    }
    return $r;
}

function my_mysqli_stmt_num_rows($stmt)
{
    $r = mysqli_stmt_num_rows($stmt);
    if ($r < 0) { // According to PHP manual, only values >= 0 are possible
        print_system_error();
        die('mysqli_stmt_num_rows() failed: ' . mysqli_stmt_error($stmt));
    }
    return $r;
}

function my_mysqli_stmt_affected_rows($stmt)
{
    $r = mysqli_stmt_affected_rows($stmt);
    if ($r < 0) {
        print_system_error();
        die('mysqli_stmt_affected_rows() failed: ' . mysqli_stmt_error($stmt));
    }
    return $r;
}

function print_add_seira_form()
{
    print "<form action='' method='post'>\n<div class='form-group row'>\n<label for='seira' class='col-sm-2 col-form-label'>Εκπαιδευτική Σειρά:</label>\n
    <div class='col-sm-10'>\n<input type='text' class='form-control' id='seira' name='seira' placeholder='137' required>\n</div>\n</div>\n
    <button type='submit' class='btn btn-primary'>Τροποποίηση</button>\n</form>\n";
}

function print_edit_seira_form($id = NULL, $seira = NULL)
{
    print "<form action='' method='post'>\n<div class='form-group row' hidden='true'>\n<label for='staticID' class='col-sm-2 col-form-label'>Α/Α</label>\n
    <div class='col-sm-10'>\n<input type='text' readonly class='form-control-plaintext' id='staticID' name='staticID' value='$id'>\n</div>\n</div>\n
    <div class='form-group row'>\n<label for='seira' class='col-sm-2 col-form-label'>Εκπαιδευτική Σειρά:</label>\n<div class='col-sm-10'>\n
    <input type='text' class='form-control' id='seira' name='seira' placeholder='$seira' required>\n</div>\n</div>\n
    <button type='submit' class='btn btn-primary'>Τροποποίηση</button>\n</form>\n";
}

function print_delete_seira_form($id = NULL, $seira = NULL)
{
    print "<form action='' method='post'>\n<div class='form-group row' hidden='true'>\n<label for='staticID' class='col-sm-2 col-form-label'>Α/Α</label>\n
    <div class='col-sm-10'>\n<input type='text' readonly class='form-control-plaintext' id='staticID' name='staticID' value='$id'>\n</div>\n</div>\n
    <div class='form-group row'>\n<label for='seira' class='col-sm-2 col-form-label'>Εκπαιδευτική Σειρά:</label>\n<div class='col-sm-10'>\n
    <input type='text' readonly class='form-control-plaintext' id='seira' name='seira' placeholder='$seira' required>\n</div>\n</div>\n
    <button type='submit' class='btn btn-warning'>Διαγραφή</button>\n</form>\n";
}

function get_student($dbc, $studID)
{
    $query = "SELECT vathmoi_abbr, spec_abbr, students_surname, students_name, seires_name FROM (vathmoi NATURAL JOIN students NATURAL JOIN specialization) NATURAL JOIN seires WHERE id_students = ? AND stud_deleted = 0";
    $stmt = my_mysqli_prepare($dbc, $query);
    my_mysqli_stmt_bind_param($stmt, 'i', $studID);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    if (my_mysqli_stmt_num_rows($stmt) == 1) {
        my_mysqli_stmt_bind_result($stmt, $vathmoi_abbr, $spec_abbr, $surname, $name, $seires_name);
        my_mysqli_stmt_fetch($stmt);
        print_edit_student_form($dbc, $studID, $vathmoi_abbr, $spec_abbr, $surname, $name, $seires_name);
    } else {
        print "<p class='alert alert-warning'>Δεν υπάρχει ο μαθητής που αναζητήσατε!</p>\n";
    }
    mysqli_stmt_close($stmt);
}

function print_add_student_form($dbc = NULL)
{
    require_once('includes/mysqli_connect.php');
    print "<form action='' method='post'>\n<div class='form-row' hidden='true'>\n
    <div class='form-group col-md-6'>\n<label for='staticID'>Α/Α</label>\n
    <input type='text' readonly class='form-control-plaintext' id='staticID' name='staticID' value=''>\n</div>\n</div>\n
    <div class='form-row'>\n<div class='form-group col-md-6'>\n
    <label for='grade'>Βαθμός</label>\n<select id='grade' name='grade' class='form-control'>\n";
    $query = "SELECT id_vathmoi, vathmoi_full, vathmoi_abbr FROM vathmoi";
    $stmt = my_mysqli_prepare($dbc, $query);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    my_mysqli_stmt_bind_result($stmt, $id_vathmoi, $vathmos_full, $vathmos_abbr);
    while (my_mysqli_stmt_fetch($stmt)) {
        print "<option value='$id_vathmoi'>$vathmos_full ($vathmos_abbr)</option>\n";
    }
    print "</select>\n</div>\n<div class='form-group col-md-6'>\n<label for='spec'>Ειδικότητα</label>\n
    <select id='spec' name='spec' class='form-control'>\n";
    $query = "SELECT id_specialization, spec_full, spec_abbr FROM specialization";
    $stmt = my_mysqli_prepare($dbc, $query);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    my_mysqli_stmt_bind_result($stmt, $id_specialization, $spec_full, $spec_abbr);
    while (my_mysqli_stmt_fetch($stmt)) {
        print "<option value='$id_specialization'>$spec_full $spec_abbr</option>\n";
    }
    print "</select>\n</div>\n</div>\n
    <div class='form-row'>\n<div class='form-group col-md-6'>\n<label for='surname'>Επώνυμο</label>\n
    <input type='text' class='form-control' id='surname' name='surname' placeholder='ΕΠΩΝΥΜΟ' value=''>\n
    </div>\n<div class='form-group col-md-6'>\n<label for='name'>Όνομα</label>\n<input type='text' class='form-control' id='name' name='name' 
    placeholder='ΟΝΟΜΑ' value=''>\n</div>\n</div>\n<div class='form-row'>\n<div class='form-group col-md-6'>\n
    <label for='inputState'>Σειρά</label>\n<select id='seira' name='seira' class='form-control'>\n";
    $query = "SELECT id_seires, seires_name FROM seires WHERE deleted = 0";
    $stmt = my_mysqli_prepare($dbc, $query);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    my_mysqli_stmt_bind_result($stmt, $id_seires, $seires_name);
    while (my_mysqli_stmt_fetch($stmt)) {
        print "<option value='$id_seires'>$seires_name</option>\n";
    }
    print "</select>\n</div>\n</div>\n<button type='submit' name='submit' class='btn btn-primary'>Προσθήκη</button>\n</form>\n";
    mysqli_stmt_close($stmt);
}

function print_edit_student_form($dbc, $studID =  NULL, $vathmos_abbr = NULL, $spec_abbr = NULL, $surname = NULL, $name = NULL, $seires_name = NULL)
{
    require_once('includes/mysqli_connect.php');
    $query = "SELECT id_vathmoi FROM vathmoi WHERE vathmoi_abbr = ?";
    $stmt = my_mysqli_prepare($dbc, $query);
    my_mysqli_stmt_bind_param($stmt, 's', $vathmos_abbr);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    if (my_mysqli_stmt_num_rows($stmt) == 1) {
        my_mysqli_stmt_bind_result($stmt, $id_vathmoi);
        my_mysqli_stmt_fetch($stmt);
    }
    print "<form action='' method='post'>\n<div class='form-row' hidden='true'>\n
    <div class='form-group col-md-6'>\n<label for='staticID'>Α/Α</label>\n
    <input type='text' readonly class='form-control-plaintext' id='staticID' name='staticID' value='$studID'>\n</div>\n</div>\n
    <div class='form-row'>\n<div class='form-group col-md-6'>\n
    <label for='grade'>Βαθμός</label>\n<select id='grade' name='grade' class='form-control'>\n
    <option selected value='$id_vathmoi'>$vathmos_abbr</option>\n";
    $query = "SELECT id_vathmoi, vathmoi_full, vathmoi_abbr FROM vathmoi";
    $stmt = my_mysqli_prepare($dbc, $query);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    my_mysqli_stmt_bind_result($stmt, $id_vathmoi, $vathmos_full, $vathmos_abbr);
    while (my_mysqli_stmt_fetch($stmt)) {
        print "<option value='$id_vathmoi'>$vathmos_full ($vathmos_abbr)</option>\n";
    }
    $query = "SELECT id_specialization FROM specialization WHERE spec_abbr = ?";
    $stmt = my_mysqli_prepare($dbc, $query);
    my_mysqli_stmt_bind_param($stmt, 's', $spec_abbr);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    if (my_mysqli_stmt_num_rows($stmt) == 1) {
        my_mysqli_stmt_bind_result($stmt, $id_specialization);
        my_mysqli_stmt_fetch($stmt);
    }
    print "</select>\n</div>\n<div class='form-group col-md-6'>\n<label for='spec'>Ειδικότητα</label>\n
    <select id='spec' name='spec' class='form-control'>\n<option selected value='$id_specialization'>$spec_abbr</option>\n";
    $query = "SELECT id_specialization, spec_full, spec_abbr FROM specialization";
    $stmt = my_mysqli_prepare($dbc, $query);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    my_mysqli_stmt_bind_result($stmt, $id_specialization, $spec_full, $spec_abbr);
    while (my_mysqli_stmt_fetch($stmt)) {
        print "<option value='$id_specialization'>$spec_full $spec_abbr</option>\n";
    }
    $query = "SELECT id_seires FROM seires WHERE seires_name = ?";
    $stmt = my_mysqli_prepare($dbc, $query);
    my_mysqli_stmt_bind_param($stmt, 's', $seires_name);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    if (my_mysqli_stmt_num_rows($stmt) == 1) {
        my_mysqli_stmt_bind_result($stmt, $id_seires);
        my_mysqli_stmt_fetch($stmt);
    }
    print "</select>\n</div>\n</div>\n
    <div class='form-row'>\n<div class='form-group col-md-6'>\n<label for='surname'>Επώνυμο</label>\n
    <input type='text' class='form-control' id='surname' name='surname' placeholder='Επώνυμο' value='$surname'>\n
    </div>\n<div class='form-group col-md-6'>\n<label for='name'>Όνομα</label>\n<input type='text' class='form-control' id='name' name='name' 
    placeholder='Όνομα' value='$name'>\n</div>\n</div>\n<div class='form-row'>\n<div class='form-group col-md-6'>\n
    <label for='seira'>Σειρά</label>\n<select id='seira' name='seira' class='form-control'>\n<option selected value='$id_seires'>$seires_name</option>\n";
    $query = "SELECT id_seires, seires_name FROM seires WHERE deleted = 0";
    $stmt = my_mysqli_prepare($dbc, $query);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    my_mysqli_stmt_bind_result($stmt, $id_seires, $seires_name);
    while (my_mysqli_stmt_fetch($stmt)) {
        print "<option value='$id_seires'>$seires_name</option>\n";
    }
    print "</select>\n</div>\n</div>\n<button type='submit' name='submit' class='btn btn-primary'>Τροποποίηση</button>\n</form>\n";
    mysqli_stmt_close($stmt);
}

function print_delete_student_form($studID =  NULL, $vathmoi_abbr =  NULL, $spec_abbr =  NULL, $surname =  NULL, $name =  NULL, $seires_name =  NULL)
{
    print "<form action='' method='post'>\n<div class='form-row' hidden='true'>\n<div class='form-group col-md-6'>\n<label for='staticID'>Α/Α</label>\n
    <input type='text' readonly class='form-control-plaintext' id='staticID' name='staticID' value='$studID'>\n</div>\n</div>\n<div class='form-row'>\n
    <div class='form-group col-md-6'>\n<label for='grade'>Βαθμός</label>\n<input type='text' readonly class='form-control' id='grade' name='grade' value='$vathmoi_abbr'>
    </div>\n<div class='form-group col-md-6'>\n<label for='spec'>Ειδικότητα</label>\n<input type='text' readonly class='form-control' id='spec' name='spec' value='$spec_abbr'>
    </div>\n</div>\n<div class='form-row'>\n<div class='form-group col-md-6'>\n<label for='surname'>Επώνυμο</label>\n
    <input type='text' readonly class='form-control' id='surname' name='surname' placeholder='Επώνυμο' value='$surname'>\n</div>\n<div class='form-group col-md-6'>\n
    <label for='name'>Όνομα</label>\n<input type='text' readonly class='form-control' id='name' name='name' placeholder='Όνομα' value='$name'>\n</div>\n</div>\n
    <div class='form-row'>\n<div class='form-group col-md-6'>\n<label for='seira'>Σειρά</label>\n
    <input type='text' readonly class='form-control' id='seira' name='seira' value='$seires_name'>\n</div>\n</div>\n
    <button type='submit' name='submit' class='btn btn-warning'>Διαγραφή</button>\n</form>";
}

function get_apousia($dbc, $id_apousies)
{
    $query = "SELECT imerominia, id_seires, seires_name, id_students, vathmoi_abbr, spec_abbr, students_surname, students_name, id_aitia, aitia, tekmiriosi, id_roles, roles, xronosfragida 
    FROM apousies NATURAL JOIN students NATURAL JOIN vathmoi NATURAL JOIN specialization NATURAL JOIN seires NATURAL JOIN aitia NATURAL JOIN roles WHERE id_apousies = ? AND apousia = 1 AND stud_deleted = 0 AND deleted = 0";
    $stmt = my_mysqli_prepare($dbc, $query);
    my_mysqli_stmt_bind_param($stmt, 'i', $id_apousies);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    if (my_mysqli_stmt_num_rows($stmt) == 1) {
        my_mysqli_stmt_bind_result($stmt, $imerominia, $id_seires, $seires_name, $id_students, $vathmoi_abbr, $spec_abbr, $students_surname, $students_name, $id_aitia, $aitia, $tekmiriosi, $id_roles, $roles, $xronosfragida);
        my_mysqli_stmt_fetch($stmt);
        print_edit_apousia_form($dbc, $id_apousies, $imerominia, $seires_name, $vathmoi_abbr, $spec_abbr, $students_surname, $students_name, $id_aitia, $aitia, $tekmiriosi);
    } else {
        print "<p class='alert alert-warning'>Δεν βρέθηκε η απουσία που αναζητήσατε!</p>\n";
    }
    mysqli_stmt_close($stmt);
}

function print_add_apousia_form($dbc = NULL)
{
    print "
    <form action='' method='post' enctype='multipart/form-data'>\n<div class='form-row'>\n<div class='form-group col-md-6'>\n
    <label for='mathitis'>Επιλογή Μαθητή</label>\n<select id='mathitis' name='mathitis' class='form-control'>\n";
    $query = "SELECT id_students, vathmoi_abbr, spec_abbr, students_surname, students_name, seires_name FROM students NATURAL JOIN vathmoi NATURAL JOIN specialization NATURAL JOIN seires WHERE stud_deleted = 0 ORDER BY seires_name, students_surname, students_name";
    $stmt = my_mysqli_prepare($dbc, $query);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    my_mysqli_stmt_bind_result($stmt, $id_students, $vathmoi_abbr, $spec_abbr, $students_surname, $students_name, $seires_name);
    while (my_mysqli_stmt_fetch($stmt)) {
        print "<option value='$id_students'>$vathmoi_abbr $spec_abbr $students_surname $students_name (Σειρά: $seires_name)</option>\n";
    }
    print "</select>\n</div>\n</div>\n<div class='form-row'>\n<div class='form-group col-md-4'>\n<label for='inputState'>Ημερομηνία</label>\n
    <input type='date' class='form-control' id='date' name='date' value=''>\n</div>\n<div class='form-group col-md-4'>\n<label for='aitia'>Αιτία</label>\n
    <select id='aitia' name='aitia' class='form-control'>\n";
    $query = "SELECT id_aitia, aitia FROM aitia";
    $stmt = my_mysqli_prepare($dbc, $query);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    my_mysqli_stmt_bind_result($stmt, $id_aitia, $aitia);
    while (my_mysqli_stmt_fetch($stmt)) {
        print "<option value='$id_aitia'>$aitia</option>\n";
    }
    print "</select>\n</div>\n<div class='form-group col-md-4'>\n<label for='tekmiriosi'>Έγγραφο τεκμηρίωσης</label>\n
    <input type='file' class='form-control' id='tekmiriosi' name='tekmiriosi'>\n</div>\n</div>\n
    <button type='submit' name='submit' class='btn btn-primary'>Προσθήκη</button>\n</form>\n";
}

function print_edit_apousia_form($dbc, $id_apousies, $imerominia, $seires_name, $vathmoi_abbr, $spec_abbr, $students_surname, $students_name, $id_aitia, $aitia, $tekmiriosi)
{
    print "<form action='' method='post' enctype='multipart/form-data'>\n<div class='form-row' hidden='true'>\n<div class='form-group col-md-6'>\n
    <label for='staticID'>Α/Α</label>\n<input type='text' readonly class='form-control-plaintext' id='staticID' name='staticID' value='$id_apousies'>\n
    </div>\n</div>\n<div class='form-row'>\n<div class='form-group col-md-3'>\n<label for='grade'>Βαθμός</label>\n
    <input type='text' readonly class='form-control' id='grade' name='grade' value='$vathmoi_abbr $spec_abbr'>\n</div>\n
    <div class='form-group col-md-3'>\n<label for='surname'>Επώνυμο</label>\n
    <input type='text' readonly class='form-control' id='surname' name='surname' value='$students_surname'>\n
    </div>\n<div class='form-group col-md-3'>\n<label for='name'>Όνομα</label>\n
    <input type='text' readonly class='form-control' id='name' name='name' value='$students_name'>\n</div>\n
    <div class='form-group col-md-3'>\n<label for='inputState'>Σειρά</label>\n
    <input type='text' readonly class='form-control' id='seira' name='seira' value='$seires_name'>\n</div>\n</div>\n<div class='form-row'>\n
    <div class='form-group col-md-4'>\n<label for='inputState'>Ημερομηνία</label>\n<input type='date' class='form-control' id='date' name='date' value='$imerominia'>\n
    </div>\n<div class='form-group col-md-4'>\n<label for='aitia'>Αιτία</label>\n<select id='aitia' name='aitia' class='form-control'>\n
    <option selected value='$id_aitia'>$aitia</option>\n";
    $query = "SELECT id_aitia, aitia FROM aitia";
    $stmt = my_mysqli_prepare($dbc, $query);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    my_mysqli_stmt_bind_result($stmt, $id_aitia, $aitia);
    while (my_mysqli_stmt_fetch($stmt)) {
        print "<option value='$id_aitia'>$aitia</option>\n";
    }
    $sub = substr($tekmiriosi, 8);
    print "</select>\n</div>\n<div class='form-group col-md-4'>\n<label for='tekmiriosi'>Έγγραφο τεκμηρίωσης: <a href='$tekmiriosi'>$sub</a></label>\n
    <input type='file' class='form-control' id='tekmiriosi' name='tekmiriosi'>\n</div>\n</div>\n<div class='form-row form-check'>\n
    <label class='form-group col-md-4 form-check-label' for='defaultCheck1'>Θέλετε να διατηρήσετε το παλαιό έγγραφο τεκμηρίωσης;</label>
    <input class='form-group col-md-1 form-check-input' type='checkbox' value='' id='defaultCheck1' name='hold'></div>
    <button type='submit' id='submit' name='submit' class='btn btn-primary'>Τροποποίηση</button>\n</form>\n";
}

function print_delete_apousia_form($id_apousies, $imerominia, $seires_name, $id_students, $vathmoi_abbr, $spec_abbr, $students_surname, $students_name, $id_aitia, $aitia, $tekmiriosi)
{
    $sub = substr($tekmiriosi, 8);
    print "<form action='' method='post' enctype='multipart/form-data'>\n<div class='form-row' hidden='true'>\n<div class='form-group col-md-6'>\n
    <label for='staticID'>Α/Α</label>\n<input type='text' readonly class='form-control-plaintext' id='staticID' name='staticID' value='$id_students'>\n
    </div>\n</div>\n<div class='form-row'>\n<div class='form-group col-md-3'>\n<label for='grade'>Βαθμός</label>\n
    <input type='text' readonly class='form-control' id='grade' name='grade' value='$vathmoi_abbr $spec_abbr'>\n</div>\n
    <div class='form-group col-md-3'>\n<label for='surname'>Επώνυμο</label>\n
    <input type='text' readonly class='form-control' id='surname' name='surname' value='$students_surname'>\n</div>\n<div class='form-group col-md-3'>\n
    <label for='name'>Όνομα</label>\n<input type='text' readonly class='form-control' id='name' name='name' value='$students_name'>\n</div>\n
    <div class='form-group col-md-3'>\n<label for='inputState'>Σειρά</label>\n<input type='text' readonly class='form-control' id='seira' name='seira' value='$seires_name'>\n
    </div>\n</div>\n<div class='form-row'>\n<div class='form-group col-md-4'>\n<label for='inputState'>Ημερομηνία</label>\n
    <input type='date' readonly class='form-control' id='date' name='date' value='$imerominia'>\n</div>\n<div class='form-group col-md-4'>\n<label for='aitia'>Αιτία</label>\n
    <input type='text' readonly id='$id_aitia' name='aitia' class='form-control' value='$aitia'>\n</div>\n<div class='form-group col-md-4'>\n
    <label for='tekmiriosi'>Έγγραφο τεκμηρίωσης</label>\n<input type='text' readonly class='form-control' id='tekmiriosi' name='tekmiriosi' value='$sub'>\n
    </div>\n</div>\n<button type='submit' id='$id_apousies' name='submit' class='btn btn-warning'>Διαγραφή</button>\n</form>\n";
}

function apousies_form($dbc, $query, $search)
{
    $stmt = my_mysqli_prepare($dbc, $query);
    my_mysqli_stmt_bind_param($stmt, 's', $search);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    my_mysqli_stmt_bind_result($stmt, $id_apousies, $imerominia, $id_seires, $seires_name, $id_students, $vathmoi_abbr, $spec_abbr, $students_surname, $students_name, $id_aitia, $aitia, $tekmiriosi, $id_roles, $roles, $xronosfragida);
    $i = 0;
?>
    <input class="form-control" id="myInput" type="text" placeholder="Αναζήτηση..">
    <br>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Α/Α</th>
                <th>Ημερομηνία</th>
                <th>Σειρά ΣΠΗΥ</th>
                <th>Βαθμός</th>
                <th>Ονοματεπώνυμο</th>
                <th>Αιτία</th>
                <th>Έγγραφο τεκμηρίωσης<br>(προαιρετικά)</th>
                <th>Καταχωρητής απουσίας</th>
                <th>Ημ/νια καταχώρησης απουσίας</th>
                <th>Επιλογές</th>
            </tr>
        </thead>
        <tbody id="myTable">
            <?php
            while (my_mysqli_stmt_fetch($stmt)) {
                $i++;
                $sub = substr($tekmiriosi, 8);
                print "<tr>\n<td>$i</td>\n<td>$imerominia</td>\n<td value='$id_seires'>$seires_name</td>\n<td value='$id_students'>$vathmoi_abbr $spec_abbr</td>\n
                <td>$students_surname $students_name</td>\n<td value='$id_aitia'>$aitia</td>\n<td><a href='$tekmiriosi'>$sub</a></td>\n<td value='$id_roles'>$roles</td>\n<td>$xronosfragida</td>\n
                <td><a href='edit_apousia.php?id=$id_apousies'>Τροποποίηση</a>/<a href='delete_apousia.php?id=$id_apousies'>Ακύρωση Απουσίας</a></td>\n</tr>\n";
            }
            ?>
        </tbody>
    </table>
    <a href='add_apousia.php'>
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
        </svg>
        Προσθήκη Απουσίας
    </a><br>
    <a href='search_apousia.php'>
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
        </svg>
        Αναζήτηση απουσιών
    </a>
    <?php
    mysqli_stmt_close($stmt);
    ?>
    <script>
        $(document).ready(function() {
            $("#myInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#myTable tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
<?php
}

function print_add_temp_apousia_form($dbc, $id_students)
{
    $query = "SELECT id_students, vathmoi_abbr, spec_abbr, students_surname, students_name FROM students NATURAL JOIN vathmoi NATURAL JOIN specialization NATURAL JOIN seires WHERE id_students = ? AND stud_deleted = 0 ORDER BY seires_name, students_surname, students_name";
    $stmt = my_mysqli_prepare($dbc, $query);
    my_mysqli_stmt_bind_param($stmt, 'i', $id_students);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    if (my_mysqli_stmt_num_rows($stmt) == 1) {
        my_mysqli_stmt_bind_result($stmt, $id_students, $vathmoi_abbr, $spec_abbr, $students_surname, $students_name);
        my_mysqli_stmt_fetch($stmt);
    }
    print "
    <form action='' method='post' enctype='multipart/form-data'>\n
        <div class='form-row' hidden='true'>\n
            <div class='form-group col-md-6'>\n
                <label for='staticID'>Α/Α</label>\n
                <input type='text' readonly class='form-control-plaintext' id='staticID' name='staticID' value='$id_students'>\n
            </div>\n
        </div>\n
        <div class='form-row'>\n
            <div class='form-group col-md-4'>\n
                <label for='grade'>Βαθμός</label>\n
                <input type='text' readonly class='form-control' id='grade' name='grade' value='$vathmoi_abbr $spec_abbr'>\n
            </div>\n
            <div class='form-group col-md-4'>\n
                <label for='surname'>Επώνυμο</label>\n
                <input type='text' readonly class='form-control' id='surname' name='surname' value='$students_surname'>\n
            </div>\n
            <div class='form-group col-md-4'>\n
                <label for='name'>Όνομα</label>\n
                <input type='text' readonly class='form-control' id='name' name='name' value='$students_name'>\n
            </div>\n
        </div>\n
        <div class='form-row'>\n
            <div class='form-group col-md-6'>\n
                <label for='aitia'>Αιτία</label>\n
                <select id='aitia' name='aitia' class='form-control'>\n";
    $query = "SELECT id_aitia, aitia FROM aitia";
    $stmt = my_mysqli_prepare($dbc, $query);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    my_mysqli_stmt_bind_result($stmt, $id_aitia, $aitia);
    while (my_mysqli_stmt_fetch($stmt)) {
        print "<option value='$id_aitia'>$aitia</option>\n";
    }
    print "
                </select>\n
            </div>\n
            <div class='form-group col-md-6'>\n
                <label for='tekmiriosi'>Έγγραφο τεκμηρίωσης</label>\n
                <input type='file' class='form-control' id='tekmiriosi' name='tekmiriosi'>\n
            </div>\n
        </div>\n
        <button type='submit' name='submit' class='btn btn-primary'>Προσθήκη</button>\n
    </form>\n";
}