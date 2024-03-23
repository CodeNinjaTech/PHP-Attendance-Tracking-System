<?php
$title = '«Αναζήτηση Απουσίας Μαθητή ΣΠΗΥ»';
include('templates/header.inc.php');
check_session();
if ($_SESSION['username'] == "diaxstis") {
require_once('includes/mysqli_connect.php');
?>
<h2>Αναζήτηση απουσιών μαθητών ΣΠΗΥ ανά μήνα του έτους</h2>
<p>Αναζητήστε απουσίες μαθητών ΣΠΗΥ</p>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $search = $_POST['month'];
    $query = "SELECT id_apousies, imerominia, id_seires, seires_name, id_students, vathmoi_abbr, spec_abbr, students_surname, students_name, id_aitia, aitia, tekmiriosi, id_roles, roles, xronosfragida 
    FROM apousies NATURAL JOIN students NATURAL JOIN vathmoi NATURAL JOIN specialization NATURAL JOIN seires NATURAL JOIN aitia NATURAL JOIN roles WHERE apousia = 1 AND stud_deleted = 0 AND deleted = 0 AND month(imerominia) = ? ORDER BY imerominia, xronosfragida, students_surname, students_name, seires_name";
    apousies_form ($dbc, $query, $search);
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