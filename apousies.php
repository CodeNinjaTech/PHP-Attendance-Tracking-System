<?php
$title = '«Απουσίες ΣΠΗΥ»';
include('templates/header.inc.php');
check_session();
if ($_SESSION['username'] == "diaxstis") {
print "<h2>Απουσιολόγιο</h2>\n<p>Προβάλλετε τις απουσίες των μαθητών της ΣΠΗΥ</p>\n";
require_once('includes/mysqli_connect.php');
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $query = "SELECT id_apousies, imerominia, id_seires, seires_name, id_students, vathmoi_abbr, spec_abbr, students_surname, students_name, id_aitia, aitia, tekmiriosi, id_roles, roles, xronosfragida 
    FROM apousies NATURAL JOIN students NATURAL JOIN vathmoi NATURAL JOIN specialization NATURAL JOIN seires NATURAL JOIN aitia NATURAL JOIN roles WHERE apousia = 1 AND stud_deleted = 0 AND deleted = 0 ORDER BY imerominia, xronosfragida, students_surname, students_name, seires_name";
    $stmt = my_mysqli_prepare($dbc, $query);
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
    mysqli_close($dbc);
    include('templates/footer.inc.php');
}
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