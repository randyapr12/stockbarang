<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "stockbarang");

// Tambah barang baru
if (isset($_POST['tambahbarangmasuk'])) {
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];

    // Insert the new item into the barangmasuk table with the current date
    $addtotable = mysqli_query($conn, "INSERT INTO barang (namabarang, deskripsi, tanggal) VALUES ('$namabarang', '$deskripsi', NOW())");

    // Get the id of the newly inserted item
    $idbarang = mysqli_insert_id($conn);

    if ($addtotable) {
        header('location:barangmasuk.php?status=success'); // Redirect to the barang masuk page
    } else {
        header('location:barangmasuk.php?status=error'); // Redirect to the barang masuk page with error status
    }
}

// Update barang
if (isset($_POST['updatebarang'])) {
    $idb = $_POST['idb'];
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];

    // Update the item in the barangmasuk table
    $updatetotable = mysqli_query($conn, "UPDATE barang SET namabarang = '$namabarang', deskripsi = '$deskripsi' WHERE idbarang = '$idb'");

    if ($updatetotable) {
        header('location:barangmasuk.php?status=update_success'); // Redirect to the barang masuk page
    } else {
        header('location:barangmasuk.php?status=update_error'); // Redirect to the barang masuk page with error status
    }
}

// Hapus barang
if (isset($_GET['hapusbarang'])) {
    $idbarang = $_GET['idb'];
    $hapus = mysqli_query($conn, "DELETE FROM barang WHERE idbarang='$idbarang'");
    if ($hapus) {
        header('location: barangmasuk.php?status=delete_success'); // Updated status
    } else {
        header('location: barangmasuk.php?status=delete_error'); // Updated status
    }
}



// Menambah stock 
if (isset($_POST['tambahstockbarang'])) {
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $addtotable = mysqli_query($conn, "INSERT INTO stock (idbarang,tanggal,keterangan,qty) VALUES ('$barangnya', NOW(),'$penerima','$qty')");
    if ($addtotable) {
        header('location:stock.php?status=success'); // Redirect to the stock page
    } else {
        header('location:stock.php?status=error'); // Redirect to the stock page with error status
    }
}

// Edit stock
if (isset($_POST['updatestockbarang'])) {
    $idstock = $_POST['idstock'];
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['keterangan'];
    $qty = $_POST['qty'];

    $update = mysqli_query($conn, "UPDATE stock SET idbarang = '$barangnya', keterangan = '$penerima', qty = '$qty' WHERE idstock = '$idstock'");
    if ($update) {
        header('location:stock.php?status=update_success');
    } else {
        header('location:stock.php?status=update_error');
    }
}


// Hapus stock
if (isset($_GET['hapusbarangmasuk'])) {
    $idstock = $_GET['idstock'];
    $hapusstock = mysqli_query($conn, "DELETE FROM stock WHERE idstock='$idstock'");
    if ($hapusstock) {
        header('location:stock.php?status=hapus_success'); // Updated status
    } else {
        header('location:stock.php?status=hapus_error'); // Updated status
    }
}



// Menambah barang yang penggunaan
if (isset($_POST['addbarangpenggunaan'])) {
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    // Cek sisa stock
    $lihat_stock = mysqli_query($conn, "SELECT SUM(qty) AS total_masuk FROM stock WHERE idbarang = '$barangnya'");
    $data_stock = mysqli_fetch_assoc($lihat_stock);
    $total_masuk = $data_stock['total_masuk'];

    $lihat_penggunaan = mysqli_query($conn, "SELECT SUM(qty) AS total_keluar FROM penggunaan WHERE idbarang = '$barangnya'");
    $data_penggunaan = mysqli_fetch_assoc($lihat_penggunaan);
    $total_keluar = $data_penggunaan['total_keluar'];

    // Hitung sisa stok
    $sisa_stok = $total_masuk - $total_keluar;

    if ($qty > $sisa_stok) {
        header('location:penggunaan.php?status=error_stok'); // Redirect to the penggunaan page with error status
    } else {
        // Tambah barang yang penggunaan
        $addtotable = mysqli_query($conn, "INSERT INTO penggunaan (idbarang, tanggal, qty, pengguna) VALUES ('$barangnya', NOW() , '$qty', '$penerima')");
        
        if ($addtotable) {
            header('location:penggunaan.php?status=success'); // Redirect to the penggunaan page with success status
        } else {
            header('location:penggunaan.php?status=error'); // Redirect to the penggunaan page with error status
        }
    }
}


// Update barang yang penggunaan
if (isset($_POST['updatepenggunaan'])) {
    $idpenggunaan = $_POST['idp'];
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    // Cek sisa stock
    $lihat_stock = mysqli_query($conn, "SELECT SUM(qty) AS total_masuk FROM stock WHERE idbarang = '$barangnya'");
    $data_stock = mysqli_fetch_assoc($lihat_stock);
    $total_masuk = $data_stock['total_masuk'];

    $lihat_penggunaan = mysqli_query($conn, "SELECT SUM(qty) AS total_keluar FROM penggunaan WHERE idpenggunaan = '$idpenggunaan'");
    $data_penggunaan = mysqli_fetch_assoc($lihat_penggunaan);
    $total_keluar = $data_penggunaan['total_keluar'];

    // Hitung sisa stok
    $sisa_stok = $total_masuk - $total_keluar;

    if ($qty > $sisa_stok) {
        header('location:penggunaan.php?status=error_stok&idpenggunaan=' . $idpenggunaan); // Redirect to the penggunaan page with error status
    } else {
        // Tambah barang yang penggunaan
        $updatetotable = mysqli_query($conn, "UPDATE penggunaan SET idbarang = '$barangnya', qty = '$qty', pengguna = '$penerima' WHERE idpenggunaan = '$idpenggunaan'");
        if ($updatetotable) {
            header('location:penggunaan.php?status=update_success&idpenggunaan=' . $idpenggunaan); // Redirect to the penggunaan page with success status
        } else {
            header('location:penggunaan.php?status=update_error&idpenggunaan=' . $idpenggunaan); // Redirect to the penggunaan page with error status
        }
    }
}


// Hapus barang yang penggunaan
if (isset($_GET['hapuspenggunaan'])) {
    $idpenggunaan = $_GET['idp'];

    $hapuspenggunaan = mysqli_query($conn, "DELETE FROM penggunaan WHERE idpenggunaan = '$idpenggunaan'");
    if ($hapuspenggunaan) {
        header('location:penggunaan.php?status=hapus_success'); // Redirect to the penggunaan page with success status
    } else {
        header('location:penggunaan.php?status=hapus_error'); // Redirect to the penggunaan page with error status
    }
}


//tambah admin baru
if(isset($_POST['addadmin'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $queryinsert = mysqli_query($conn, "insert into login (email, password) values ('$email', '$password')");

    if($queryinsert){
        //if berhasil
        header('location:admin.php');
    } else{
        header('location:admin.php');
    }
}

//update data admin
if(isset($_POST['updateadmin'])){
    $emailbaru = $_POST['emailadmin'];
    $passwordbaru = $_POST['passwordbaru'];
    $idnya = $_POST['id'];

    $queryupdate = mysqli_query($conn, "update login set email = '$emailbaru', password = '$passwordbaru' where iduser = '$idnya'");

    if($queryupdate){
        //if berhasil
        header('location:admin.php');
    } else{
        header('location:admin.php');
    }
};

//delete admin
if(isset($_POST['hapusadmin'])){
    $id = $_POST['id'];

    $querydelete = mysqli_query($conn, "delete from login where iduser = '$id'");

    if($querydelete){
        //if berhasil
        header('location:admin.php');
    } else{
        header('location:admin.php');
    }
}

?>