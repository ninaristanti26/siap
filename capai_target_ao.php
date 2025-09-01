<?php
ob_start();
session_start();
include "koneksi.php";
include "atas_ao.php";

if (isset($_GET['id_ao'])) {
    $_SESSION['id_ao'] = $_GET['id_ao'];
}
$id_ao = $_SESSION['id_ao'] ?? '';

function renderStatusIcon($status) {
    return (string)$status === '1'
        ? "<i class='far fa-thumbs-up' style='font-size:15px;color:green'></i>"
        : "<i class='far fa-thumbs-down' style='font-size:15px;color:red'></i>";
}
?>

<!-- External CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link href="datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">
<link href="datatables-responsive/dataTables.responsive.css" rel="stylesheet">

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row mb-4">
      <div class="col-12 col-xl-8">
        <h3 class="font-weight-bold">Welcome <?= htmlspecialchars($id_ao) ?></h3>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col">
        <h4>Pencapaian Target</h4>
      </div>
    </div>

    <div class="container">
      <form method="get" action="">
        <input type="hidden" name="id_ao" value="<?= htmlspecialchars($id_ao) ?>">
        <div class="row justify-content-center">
          <div class="col-md-6">
            <div class="form-group">
              <label for="tahun">Pilih Tahun Data</label>
              <select name="tahun" class="form-control" id="tahun" required>
                <option value="">-- Pilih Tahun --</option>
                <?php
                $msql = "SELECT DISTINCT YEAR(d.tgl_data) AS tahun 
                         FROM debitur_npl d
                         INNER JOIN target_ao t ON YEAR(d.tgl_data) = t.tahun
                         ORDER BY tahun DESC";
                $hasil = $mysqli->query($msql);
                $selectedTahun = $_GET['tahun'] ?? '';
                
                while ($m_row = $hasil->fetch_array()) {
                       $tahun = $m_row['tahun'];
                       $selected = $tahun === $selectedTahun ? 'selected' : '';
                  echo "<option value='$tahun' $selected>$tahun</option>";
                }
                ?>
              </select>
            </div>
          </div>
        </div>
        <div class="text-center mb-3">
          <button type="submit" class="btn btn-primary btn-custom">Tampilkan</button>
          <a href="?id_ao=<?= $id_ao ?>" class="btn btn-secondary btn-custom">Reset</a>
        </div>
      </form>
      <style>
        .btn-custom:hover {
          background-color: #007bff;
          color: #fff;
          transform: scale(1.05);
        }
        .btn-custom {
          margin-right: 5px;
        }
      </style>

<?php include("footer.php"); ?>

<!-- DataTables JS -->
<script src="datatables/js/jquery.dataTables.min.js"></script>
<script src="datatables-plugins/dataTables.bootstrap.min.js"></script>
<script src="datatables-responsive/dataTables.responsive.js"></script>

<script>
$(document).ready(function() {
  $('#dataTables-example').DataTable({ responsive: true });
});
</script>
