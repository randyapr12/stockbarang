<?php
require 'function.php';
require 'cek.php';

// Initialize variables for date filtering
$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : '';

// Fetch data from the penggunaan table including the date with filtering
$query = "SELECT * FROM penggunaan";
if ($startDate && $endDate) {
    // Use <= for the end date to include the entire day
    $query .= " AND p.tanggal >= '$startDate' AND p.tanggal <= '$endDate 23:59:59'";
}
$ambilsemuadatastock = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Penggunaan Barang</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
</head>
<body>
<div class="container">
    <h2>Export Penggunaan Barang</h2>
    <h4>(Inventory)</h4>

    <!-- Date Filter Form -->
    <form method="POST" class="mb-4">
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="start_date">Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?= $startDate ?>">
            </div>
            <div class="form-group col-md-4">
                <label for="end_date">End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $endDate ?>">
            </div>
            <div class="form-group col-md-4">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary btn-block">Filter</button>
            </div>
        </div>
    </form>

    <div class="data-tables datatable-dark">
        <table class="table table-bordered" id="mauexport" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Pengguna</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                while ($data = mysqli_fetch_array($ambilsemuadatastock)) {
                    $idp = $data['idpenggunaan'];
                    $idb = $data['idbarang'];
                    $tanggal = $data['tanggal'];
                    $qty = $data['qty'];
                    $penerima = $data['pengguna'];

                    // Ambil nama barang dari tabel barang 
                    $query_barang = "SELECT * FROM barang WHERE idbarang = '$idb'";
                    $ambil_nama_barang = mysqli_query($conn, $query_barang);
                    if (!$ambil_nama_barang) {
                        die("Query Error: " . mysqli_error($conn));
                    }
                    $nama_barang = mysqli_fetch_array($ambil_nama_barang);
                    if ($nama_barang) {
                        $namabarang = $nama_barang['namabarang'];
                    } else {
                        $namabarang = 'Barang tidak ditemukan';
                    }
                ?>
                <tr>
                    <td><?=$i++;?></td>
                    <td><?= $tanggal; ?></td>
                    <td><?= $namabarang; ?></td>
                    <td><?= $qty; ?></td>
                    <td><?= $penerima; ?></td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#mauexport').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
});
</script>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>

</body>
</html>
