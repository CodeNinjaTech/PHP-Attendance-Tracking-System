<?php
$title = '«Σειρές ΣΠΗΥ»';
include('templates/header.inc.php');
check_session();
if ($_SESSION['username'] == "diaxstis") {
require_once('includes/mysqli_connect.php');
?>
<h2>Σειρές ΣΠΗΥ</h2>
<p>Προβάλλετε τις Εκπαιδευτικές Σειρές ΣΠΗΥ</p>
<input class="form-control" id="myInput" type="text" placeholder="Αναζήτηση..">
<br>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Α/Α</th>
            <th>Ονομα Σειράς</th>
            <th>Επιλογές</th>
        </tr>
    </thead>
    <tbody id="myTable">
        <?php
        $query = 'SELECT id_seires, seires_name FROM seires WHERE deleted = 0';
        $stmt = my_mysqli_prepare($dbc, $query);
        my_mysqli_stmt_execute($stmt);
        my_mysqli_stmt_store_result($stmt);
        my_mysqli_stmt_bind_result($stmt, $id, $seira);
        $i = 0;
        while (my_mysqli_stmt_fetch($stmt)) {
            $i ++;
            print "<tr>\n<td>$i</td>\n<td>$seira</td>\n<td><a href='seira_students.php?id=$id'>Προβολή</a>/
            <a href='edit_seira.php?id=$id'>Τροποποίηση</a>/<a href='delete_seira.php?id=$id'>Αφαίρεση Σειράς</a></td>\n</tr>\n";
        }
        mysqli_stmt_close($stmt);
        mysqli_close($dbc);
        ?>
    </tbody>
</table>
<a href='add_seira.php'>
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
    </svg>
    Προσθήκη Σειράς
</a>
<?php
include('templates/footer.inc.php');
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