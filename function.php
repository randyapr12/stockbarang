<?php
session_start();

$conn = mysqli_connect("localhost","root","","stockbarang");

// tambah barang baru
if(isset($_POST['addnewbarang'])) {
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];

    // Insert the new item into the stock table with the current date
    $addtotable = mysqli_query($conn, "INSERT INTO stock (namabarang, deskripsi, stock, tanggal) VALUES ('$namabarang', '$deskripsi', '$stock', NOW())");
    
    if($addtotable) {
        header('location:index.php?status=success');
    } else {
        header('location:index.php?status=error');
    }
}


// menambah barang yang masuk
if(isset($_POST['barangmasuk'])) {
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstocksekarang = mysqli_query($conn,"SELECT * FROM stock where idbarang='$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildatanya['stock'];
    $tambahkanstocksekarangdenganquantity = $stocksekarang + $qty;

    $addtomasuk = mysqli_query($conn,"insert into masuk(idbarang, keterangan, qty) values('$barangnya','$penerima','$qty')");
    $updatestockmasuk = mysqli_query($conn,"update stock set stock ='$tambahkanstocksekarangdenganquantity' where idbarang='$barangnya'");
    if($addtomasuk && $updatestockmasuk) {
        header('location:masuk.php?status=success_add');
    } else {
        header('location:masuk.php?status=error_add');
    }
}



// menambah barang yang penggunaan
if(isset($_POST['addbarangpenggunaan'])) {
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstocksekarang = mysqli_query($conn,"SELECT * FROM stock where idbarang='$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildatanya['stock'];

    if($stocksekarang >= $qty){
        // kalau barang cukup
        $tambahkanstocksekarangdenganquantity = $stocksekarang - $qty;

        $addtopenggunaan = mysqli_query($conn,"insert into penggunaan(idbarang, penerima, qty) values('$barangnya','$penerima','$qty')");
        $updatestockmasuk = mysqli_query($conn,"update stock set stock ='$tambahkanstocksekarangdenganquantity' where idbarang='$barangnya'");
        if($addtopenggunaan && $updatestockmasuk) {
            header('location:penggunaan.php?status=success_add');
        } else {
            header('location:penggunaan.php?status=error_add');
        }
    } else {
        echo '
        <script>
            alert("Stock tidak mencukupi untuk pengeluaran.");
            window.location.href="penggunaan.php";
        </script>
        ';
    }
}


// update info barang
if (isset($_POST['updatebarang'])) {
    $idb = $_POST['idb'];
    $namabarang = mysqli_real_escape_string($conn, $_POST['namabarang']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);

    $update = mysqli_query($conn, "UPDATE stock SET namabarang = '$namabarang', deskripsi = '$deskripsi' WHERE idbarang = '$idb'");
    if (!$update) {
        echo mysqli_error($conn);
        header('location:index.php?status=update_error');
    } else {
        header('location:index.php?status=update_success');
    }
}

// delete barang stock
if(isset($_POST['hapusbarang'])){
    $idb = $_POST['idb'];

    $hapus = mysqli_query($conn, "delete from stock where idbarang = '$idb'");
    if($hapus){
        header('location:index.php?status=delete_success');
    } else {
        header('location:index.php?status=delete_error');
    }
}


// mengupdate barang masuk
if(isset($_POST['updatebarangmasuk'])){
    $idb = $_POST['idb'];
    $idm = $_POST['idm'];
    $deskripsi = $_POST['keterangan'];
    $qty = $_POST['qty'];

    $lihatstock = mysqli_query($conn, "select * from stock where idbarang = '$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrg = $stocknya['stock'];

    $qtyskrg = mysqli_query($conn, "select * from masuk where idmasuk= '$idm'");
    $qtynya = mysqli_fetch_array($qtyskrg);
    $qtyskrg = $qtynya['qty'];

    if($qty > $qtyskrg){
        $selisih = $qty - $qtyskrg;
        $kurangin = $stockskrg + $selisih;
        $kuranginstocknya = mysqli_query($conn, "update stock set stock = '$kurangin' where idbarang = '$idb'");
        $updatenya = mysqli_query($conn, "update masuk set qty = '$qty', keterangan = '$deskripsi' where idmasuk = '$idm'");
        if($kuranginstocknya && $updatenya){
            header('location:masuk.php?status=success_update');
        } else {
            header('location:masuk.php?status=error_update');
        }
    } else {
        $selisih = $qtyskrg - $qty;
        $kurangin = $stockskrg - $selisih;
        $kuranginstocknya = mysqli_query($conn, "update stock set stock = '$kurangin' where idbarang = '$idb'");
        $updatenya = mysqli_query($conn, "update masuk set qty = '$qty', keterangan = '$deskripsi' where idmasuk = '$idm'");
        if($kuranginstocknya && $updatenya){
            header('location:masuk.php?status=success_update');
        } else {
            header('location:masuk.php?status=error_update');
        }
    }
};


// delete barang masuk
if(isset($_POST['hapusbarangmasuk'])){
    $idb = $_POST['idb'];
    $qty = $_POST['kty'];
    $idm = $_POST['idm'];

    $getdatastock = mysqli_query($conn, "select * from stock where idbarang = '$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stok = $data['stock'];

    $selisih = $stok - $qty;

    $update = mysqli_query($conn, "update stock set stock = '$selisih' where idbarang = '$idb'");
    $hapusdata = mysqli_query($conn, "delete from masuk where idmasuk = '$idm'");

    if($update && $hapusdata){
        header('location:masuk.php?status=success_delete');
    } else {
        header('location:masuk.php?status=error_delete');
    }
}

// mengupdate barang penggunaan
if(isset($_POST['updatebarangpenggunaan'])){
    $idb = $_POST['idb'];
    $idp = $_POST['idp'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty']; // quantiti baru input user

    // ambil stock barang saat ini
    $lihatstock = mysqli_query($conn, "select * from stock where idbarang = '$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrg = $stocknya['stock'];

    // quantiti barang saat ini
    $qtyskrg = mysqli_query($conn, "select * from penggunaan where idpenggunaan = '$idp'");
    $qtynya = mysqli_fetch_array($qtyskrg);
    $qtyskrg = $qtynya['qty'];

    if($qty > $qtyskrg){
        $selisih = $qty - $qtyskrg;
        $kurangin = $stockskrg - $selisih;

        if($selisih <= $stockskrg){
            $kuranginstocknya = mysqli_query($conn, "update stock set stock = '$kurangin' where idbarang = '$idb'");
            $updatenya = mysqli_query($conn, "update penggunaan set qty = '$qty', penerima = '$penerima' where idpenggunaan = '$idp'");
            if($kuranginstocknya && $updatenya){
                header('location:penggunaan.php?status=success_update');
            } else {
                header('location:penggunaan.php?status=error_update');
            }
        } else {
            echo '
            <script>
                alert("Stock saat ini tidak mencukupi");
                window.location.href="penggunaan.php";
            </script>
            ';
        }
    } else {
        $selisih = $qtyskrg - $qty;
        $kurangin = $stockskrg + $selisih;
        $kuranginstocknya = mysqli_query($conn, "update stock set stock = '$kurangin' where idbarang = '$idb'");
        $updatenya = mysqli_query($conn, "update penggunaan set qty = '$qty', penerima = '$penerima' where idpenggunaan = '$idp'");
        if($kuranginstocknya && $updatenya){
            header('location:penggunaan.php?status=success_update');
        } else {
            header('location:penggunaan.php?status=error_update');
        }
    }
}


// delete barang penggunaan
if(isset($_POST['hapusbarangpenggunaan'])){
    $idb = $_POST['idb'];
    $qty = $_POST['kty'];
    $idp = $_POST['idp'];

    $getdatastock = mysqli_query($conn, "select * from stock where idbarang = '$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stok = $data['stock'];

    $selisih = $stok + $qty;

    $update = mysqli_query($conn, "update stock set stock = '$selisih' where idbarang = '$idb'");
    $hapusdata = mysqli_query($conn, "delete from penggunaan where idpenggunaan = '$idp'");

    if($update && $hapusdata){
        header('location:penggunaan.php?status=success_delete');
    } else {
        header('location:penggunaan.php?status=error_delete');
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
};

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