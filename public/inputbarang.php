<div class="card mb-4">
  <div class="card-header">Input Data Barang</div>
  <div class="card-body">
    <form method="POST" enctype="multipart/form-data">
      <div class="col-md-12">
        <div class="form-group">
          <label for="">Kode Barang</label>
          <input type="text" name="kode_barang" class="form-control" placeholder="Masukkan Kode Barang">
        </div> <br>
        <div class="form-group">
          <label for="">Nama Barang</label>
          <input type="text" name="nama_barang" class="form-control" placeholder="Masukkan Nama Barang">
        </div> <br>
        <div class="form-group">
          <label for="">ID Kategori</label>
          <select name="id_kategori" class="form-control">
            <?php include "koneksi.php";
            $sql = mysqli_query($db, "SELECT * from tbl_kategori");

            while ($data = mysqli_fetch_array($sql)) {
            ?>
              <option hidden>Pilih Kategori</option>
              <option value="<?php echo $data['id_kategori'] ?>"><?php echo $data['nama_kategori'] ?></option>
            <?php  } ?>
          </select>
        </div> <br>
        <div class="form-group">
          <label for="">ID Supplier</label>
          <select name="id_supplier" class="form-control">
            <?php include "koneksi.php";
            $sql = mysqli_query($db, "SELECT * from tbl_supplier");

            while ($data = mysqli_fetch_array($sql)) {
            ?>
              <option hidden>Pilih Supplier</option>
              <option value="<?php echo $data['id_supplier'] ?>"><?php echo $data['nama_supplier'] ?></option>
            <?php  } ?>
          </select>
        </div> <br>
        <div class="form-group">
          <label for="">Harga</label>
          <input type="number" name="harga" class="form-control" placeholder="Masukkan Harga Barang">
        </div> <br>
        <div class="form-group">
          <label for="">Stock</label>
          <input type="number" name="stock" class="form-control" placeholder="Masukkan Stock Barang">
        </div> <br>
        <div class="form-group">
          <label for="">Tanggal</label>
          <input type="date" name="tanggal" class="form-control">
        </div> <br>
        <div class="form-group">
          <label for="">Keterangan</label>
          <textarea type="text" rows="4" name="keterangan" class="form-control" placeholder="Masukkan Keterangan Barang"></textarea>
        </div> <br>

        <input type="submit" class="btn btn-success" value="Tambah Barang" />
      </div>
    </form>
  </div>
</div>

<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  include "koneksi.php";

  $kode_barang = $_POST['kode_barang'];
  $nama_barang = $_POST['nama_barang'];
  $id_kategori = $_POST['id_kategori'];
  $id_supplier = $_POST['id_supplier'];
  $harga = $_POST['harga'];
  $stock = $_POST['stock'];
  $tanggal = $_POST['tanggal'];
  $keterangan = $_POST['keterangan'];

  $sql = mysqli_query($db, "INSERT INTO tbl_barang VALUES ('$kode_barang', '$nama_barang', '$id_kategori', '$id_supplier', $harga, $stock, '$tanggal', '$keterangan')");

  if ($sql) {
    echo "
    <script>
    window.location.replace('admin.php?page=databarang'),
      window.alert('Data berhasil dibuat!')
    </script>";
  } else {
    die("Error di SQL");
  }
} else {
}
?>