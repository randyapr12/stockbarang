<?php
require 'function.php';
require 'cek.php';

// Initialize variables for date filtering
$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : '';

// Fetch data from the barang table including the date with filtering
$query = "SELECT * FROM barang";
if ($startDate && $endDate) {
    // Use <= for the end date to include the entire day
    $query .= " WHERE tanggal >= '$startDate' AND tanggal <= '$endDate 23:59:59'";
}

// Execute the query and check for errors
$ambilsemuadatastock = mysqli_query($conn, $query);
if (!$ambilsemuadatastock) {
    die("Query Error: " . mysqli_error($conn)); // Display error if query fails
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Barang Data</title>
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
    <h2>Export Barang</h2>
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
                    <th>Nama Barang</th>
                    <th>Deskripsi</th>
                    <th>Stock Terkini</th>
                    <th>Tanggal Ditambahkan</th> 
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                while ($data = mysqli_fetch_array($ambilsemuadatastock)) {
                    $namabarang = $data['namabarang'];
                    $deskripsi = $data['deskripsi'];
                    $idb = $data['idbarang'];

                    // Cek sisa stock
                    $lihat_stock = mysqli_query($conn, "SELECT SUM(qty) AS total_masuk FROM stock WHERE idbarang = '$idb'");
                    $data_stock = mysqli_fetch_assoc($lihat_stock);
                    $total_masuk = $data_stock['total_masuk'];

                    $lihat_penggunaan = mysqli_query($conn, "SELECT SUM(qty) AS total_keluar FROM penggunaan WHERE idbarang = '$idb'");
                    $data_penggunaan = mysqli_fetch_assoc($lihat_penggunaan);
                    $total_keluar = $data_penggunaan['total_keluar'];

                    // Hitung sisa stok
                    $sisa_stok = $total_masuk - $total_keluar;
                ?>
                <tr>
                    <td><?=$i++;?></td>
                    <td><?php echo $namabarang; ?></td>
                    <td><?php echo $deskripsi; ?></td>
                    <td><?php echo $sisa_stok; ?></td>
                    <td><?=date('d-m-Y H:i:s', strtotime($data['tanggal']));?></td> 
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
