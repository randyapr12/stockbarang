<?php
session_start();

$conn = mysqli_connect("localhost","root","","stockbarang");

//tambah barang baru
if(isset($_POST['addnewbarang'])) {
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];

    $addtotable = mysqli_query($conn, "insert into stock (namabarang, deskripsi, stock) values('$namabarang','$deskripsi','$stock')");
    if($addtotable) {
        header('location:index.php');
    } else {
        echo'gagal';
        header('location:index.php');
    }
};

//menambah barang yang masuk
if(isset($_POST['barangmasuk'])) {
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstocksekarang = mysqli_query($conn,"SELECT * FROM stock where idbarang='$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildatanya['stock'];
    $tambahkanstocksekarangdenganquantity = $stocksekarang+$qty;

    $addtomasuk = mysqli_query($conn,"insert into masuk(idbarang, keterangan, qty) values('$barangnya','$penerima','$qty')");
    $updatestockmasuk = mysqli_query($conn,"update stock set stock ='$tambahkanstocksekarangdenganquantity' where idbarang='$barangnya'");
    if($addtomasuk&&$updatestockmasuk) {
        header('location:masuk.php');
    } else {
        echo'gagal';
        header('location:masuk.php');
    }
}


//menambah barang yang keluar
if(isset($_POST['addbarangkeluar'])) {
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstocksekarang = mysqli_query($conn,"SELECT * FROM stock where idbarang='$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildatanya['stock'];
    $tambahkanstocksekarangdenganquantity = $stocksekarang-$qty;

    $addtokeluar = mysqli_query($conn,"insert into keluar(idbarang, penerima, qty) values('$barangnya','$penerima','$qty')");
    $updatestockmasuk = mysqli_query($conn,"update stock set stock ='$tambahkanstocksekarangdenganquantity' where idbarang='$barangnya'");
    if($addtokeluar&&$updatestockmasuk) {
        header('location:keluar.php');
    } else {
        echo'gagal';
        header('location:keluar.php');
    }
};

//update info barang
if(isset($_POST['updatebarang'])){
    $idb = $_POST['idb'];
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];

    $update = mysqli_query($conn,"update stock set namabarang = '$namabarang', deskripsi = '$deskripsi' where idbarang = '$idb'");
    if($update){
        header('location:index.php');
    } else {
        echo'gagal';
        header('location:index.php');
    }
};

//delete barang stock
if(isset($_POST['hapusbarang'])){
    $idb = $_POST['idb'];

    $hapus = mysqli_query($conn, "delete from stock where idbarang = '$idb'");
    if($hapus){
        header('location:index.php');
    } else {
        echo'gagal';
        header('location:index.php');
    }
};

//mengupdate barang masuk
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

    if($qty>$qtyskrg){
        $selisih = $qty-$qtyskrg;
        $kurangin = $stockskrg + $selisih;
        $kuranginstocknya = mysqli_query($conn, "update stock set stock = '$kurangin' where idbarang = '$idb'");
        $updatenya = mysqli_query($conn, "update masuk set qty = '$qty', keterangan = '$deskripsi' where idmasuk = '$idm'");
            if($kuranginstocknya&&$updatenya){
                header('location:masuk.php');
            } else {
                echo'gagal';
                header('location:masuk.php');
            }
    } else{
        $selisih = $qtyskrg-$qty;
        $kurangin = $stockskrg - $selisih;
        $kuranginstocknya = mysqli_query($conn, "update stock set stock = '$kurangin' where idbarang = '$idb'");
        $updatenya = mysqli_query($conn, "update masuk set qty = '$qty', keterangan = '$deskripsi' where idmasuk = '$idm'");
            if($kuranginstocknya&&$updatenya){
                header('location:masuk.php');
            } else {
                echo'gagal';
                header('location:masuk.php');
            }

    }
};

//delete barang masuk
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

    if($update&&$hapusdata){
        header('location:masuk.php');
    } else {
        header('location:masuk.php');
    }
};

//mengupdate barang keluar
if(isset($_POST['updatebarangkeluar'])){
    $idb = $_POST['idb'];
    $idk = $_POST['idk'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $lihatstock = mysqli_query($conn, "select * from stock where idbarang = '$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrg = $stocknya['stock'];

    $qtyskrg = mysqli_query($conn, "select * from keluar where idkeluar = '$idk'");
    $qtynya = mysqli_fetch_array($qtyskrg);
    $qtyskrg = $qtynya['qty'];

    if($qty>$qtyskrg){
        $selisih = $qty-$qtyskrg;
        $kurangin = $stockskrg - $selisih;
        $kuranginstocknya = mysqli_query($conn, "update stock set stock = '$kurangin' where idbarang = '$idb'");
        $updatenya = mysqli_query($conn, "update keluar set qty = '$qty', penerima = '$penerima' where idkeluar = '$idk'");
            if($kuranginstocknya&&$updatenya){
                header('location:keluar.php');
            } else {
                echo'gagal';
                header('location:keluar.php');
            }
    } else{
        $selisih = $qtyskrg-$qty;
        $kurangin = $stockskrg + $selisih;
        $kuranginstocknya = mysqli_query($conn, "update stock set stock = '$kurangin' where idbarang = '$idb'");
        $updatenya = mysqli_query($conn, "update keluar set qty = '$qty', penerima = '$penerima' where idkeluar = '$idk'");
            if($kuranginstocknya&&$updatenya){
                header('location:keluar.php');
            } else {
                echo'gagal';
                header('location:keluar.php');
            }

    }
};

//delete barang keluar
if(isset($_POST['hapusbarangkeluar'])){
    $idb = $_POST['idb'];
    $qty = $_POST['kty'];
    $idk = $_POST['idk'];

    $getdatastock = mysqli_query($conn, "select * from stock where idbarang = '$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stok = $data['stock'];

    $selisih = $stok + $qty;

    $update = mysqli_query($conn, "update stock set stock = '$selisih' where idbarang = '$idb'");
    $hapusdata = mysqli_query($conn, "delete from keluar where idkeluar = '$idk'");

    if($update&&$hapusdata){
        header('location:keluar.php');
    } else {
        header('location:keluar.php');
    }
};


?>

