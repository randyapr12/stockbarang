<?php
require 'function.php';
require 'cek.php';

// Initialize variables for date filtering
$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : '';

// Fetch data from the barangmasuk table including the date with filtering
// Query untuk mengambil data barang, stok, dan penggunaan
$query = "SELECT * FROM barang";
if ($startDate && $endDate) {
    $query .= " WHERE tanggal >= '$startDate' AND tanggal <= '$endDate 23:59:59'";
}
$query .= " ORDER BY tanggal DESC";


// // Execute the query and check for errors
$ambilsemuadatastock = mysqli_query($conn, $query);
if (!$ambilsemuadatastock) {
    die("Query Error: " . mysqli_error($conn)); // Menampilkan error jika query gagal
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Stok Barang Masuk</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
    <script>
    window.onload = function() {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');

        if (status === 'success') {
            alert('Barang berhasil ditambahkan!');
        } else if (status === 'error') {
            alert('Gagal menambahkan barang. Silakan coba lagi.');
        } else if (status === 'update_success') {
            alert('Barang berhasil diperbarui!');
        } else if (status === 'update_error') {
            alert('Gagal memperbarui barang. Silakan coba lagi.');
        } else if (status === 'delete_success') {
            alert('Barang berhasil dihapus!');
        } else if (status === 'delete_error') {
            alert('Gagal menghapus barang. Silakan coba lagi.');
        }
    };
</script>

</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand" href="barangmasuk.php">
            <img src="images/logo.png" alt="Logo" style="width: 30px; height: auto; margin-right: 10px;" />
            Stok Barang Masuk
        </a>
        <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <a class="nav-link" href="barangmasuk.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-box"></i></div>
                            Master Barang
                        </a>
                        <a class="nav-link" href="stock.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-sign-in-alt"></i></div>
                            Barang Masuk
                        </a>
                        <a class="nav-link" href="warehouse.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-warehouse"></i></div>
                            Stock Barang
                        </a>
                        <a class="nav-link" href="penggunaan.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div>
                            Penggunaan Barang
                        </a>
                        <a class="nav-link" href="admin.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-user-cog"></i></div>
                            Kelola Admin
                        </a>
                        <a class="nav-link" href="logout.php">
                            <div class="sb-nav-link-icon"></div>
                            Logout
                        </a>
                    </div>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <h1 class="mt-4">Master Barang</h1>
                    <div class="card mb-4">
                        <div class="card-header">
                            <!-- Date Filter Form -->
                            <form method="POST" class="mb-4">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="start_date">Start Date</label>
                                        <input type="date" class="form-control form-control-sm" id="start_date" name="start_date" value="<?= htmlspecialchars($startDate); ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="end_date">End Date</label>
                                        <input type="date" class="form-control form-control-sm" id="end_date" name="end_date" value="<?= htmlspecialchars($endDate); ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block btn-sm">Filter</button>
                                    </div>
                                </div>
                            </form>
                            <!-- Button to Open the Modal -->
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                                Tambah Barang
                            </button>
                            <a href="export_master.php" class="btn btn-info">Export data</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Barang</th>
                                            <th>Deskripsi</th>                                                                                        
                                            <th>Tanggal Dibuat</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        while ($data = mysqli_fetch_array($ambilsemuadatastock)) {
                                            $namabarang = $data['namabarang'];
                                            $deskripsi = $data['deskripsi'];                                                                                      
                                            $tanggalmasuk =date("d F Y", strtotime($data['tanggal']));
                                            $idb = $data['idbarang'];
                                        ?>
                                            <tr>
                                                <td><?= $i++; ?></td>
                                                <td><?= $namabarang; ?></td>
                                                <td><?= $deskripsi; ?></td>                                                
                                                <td><?= $tanggalmasuk; ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit<?= $idb; ?>">
                                                        Edit
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete<?= $idb; ?>">
                                                        Hapus
                                                    </button>
                                                </td>
                                            </tr>

                                            <div class="modal fade" id="edit<?= $idb; ?>">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Edit Barang</h4>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form method="post">
                                                                <input type="hidden" name="idb" value="<?= $idb; ?>">
                                                                <div class="form-group">
                                                                    <label for="namabarang">Nama Barang</label>
                                                                    <input type="text" name="namabarang" class="form-control" value="<?= $namabarang; ?>" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="deskripsi">Deskripsi</label>
                                                                    <input type="text" name="deskripsi" class="form-control" value="<?= $deskripsi; ?>" required>
                                                                </div>
                                                                <button type="submit" class="btn btn-primary" name="updatebarang">Simpan</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal fade" id="delete<?= $idb; ?>">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Hapus Barang?</h4>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Apakah Anda yakin ingin menghapus <?= $namabarang; ?>?
                                                            <form method="get">
                                                                <input type="hidden" name="idb" value="<?= $idb; ?>">
                                                                <button type="submit" class="btn btn-danger" name="hapusbarang">Hapus</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/datatables-demo.js"></script>
</body>

<!-- The Modal -->
<div class="modal fade" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Tambah Barang</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <form method="post">
                <div class="modal-body">
                    <input type="text" name="namabarang" placeholder="Nama Barang" class="form-control" required>
                    <br>
                    <select name="deskripsi" class="form-control" required>
                        <option value="" disabled selected>Jenis Barang</option>
                        <option value="bahan baku">Bahan Baku</option>
                    </select>
                    <br>
                    <button type="submit" class="btn btn-primary" name="tambahbarangmasuk">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

</html>