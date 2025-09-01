<?php
ob_start();
session_start();
include "koneksi.php";
include "atas_kasie.php";

function renderStatusIcon($status) {
    return $status == '1'
        ? "<i class='far fa-thumbs-up' style='font-size:15px;color:green'></i>"
        : "<i class='far fa-thumbs-down' style='font-size:15px;color:red'></i>";
}
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link href="datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">
<link href="datatables-responsive/dataTables.responsive.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


<div class="main-panel">
  <div class="content-wrapper">
    <!-- Welcome Section -->
    <div class="row mb-4">
      <div class="col-12 col-xl-8">
        <h3 class="font-weight-bold">Welcome <?= htmlspecialchars($_SESSION['id_ao']) ?></h3>
      </div>
    </div>

    <!-- Informasi AO -->
    <?php
    include("getCode/get_pipe.php");
    if (!empty($options)) {
      foreach ($options as $data) {
    ?>
    <div class="table-responsive mb-3">
      <table class="table">
        <thead class="text-primary">
          <tr>
            <th width="150">Kantor Cabang</th><th width="30">:</th><td><?= $data['nama_cabang'] ?></td>
          </tr>
         </thead>
      </table>
    </div>
    <?php }} else { echo "<p class='text-muted'>Belum Ada Data</p>"; } ?>

    <hr>

    <!-- Tombol Tambah -->
    <div class="mb-3">
        <h4>Data Target AO</h4>
      <button type="button" class="btn btn-primary btn-sm text-white" onclick="toggleFormTargetAO()">+ Tambah Data</button>
    </div>

    <!-- Tabel Debitur -->
    <div class="table-responsive">
        <table class="table table-hover table-bordered" id="dataTables-example" width="1300px">
        <thead class="bg-primary text-center text-white">
          <tr>
            <th class="text-center">No.</th>
            <th class="text-center">Petugas AO</th>
            <th class="text-center">Tahun</th>
            <th class="text-center">Target Penyelesaian NPL</th>
            <th class="text-center"></th>
          </tr>
        </thead>
        <tbody>
          <?php
$kode_cabang = isset($_GET['kode_cabang']) ? $_GET['kode_cabang'] : '';

if (!empty($kode_cabang)) {
    $queri = "SELECT *
              FROM target_ao, cabang
              JOIN ao ON cabang.kode_cabang = ao.kode_cabang
              JOIN debitur_npl ON debitur_npl.id_ao = ao.id_ao 
              AND debitur_npl.kode_cabang = cabang.kode_cabang
              WHERE cabang.kode_cabang = ?
              GROUP BY target_ao.id_ao";

    if ($stmt = $mysqli->prepare($queri)) {
        $stmt->bind_param("s", $kode_cabang);
        $stmt->execute();
        $result = $stmt->get_result();

        $no = 1;
        while ($data = $result->fetch_assoc()) {
            $bayar_pokok = $data['bayar_pokok'] ?? 0;
            $bayar_bunga = $data['bayar_bunga'] ?? 0;
            $total_bayar = $bayar_pokok + $bayar_bunga;

            echo "<tr>
                    <td class='text-center'>{$no}</td>
                    <td class='text-left'>{$data['nama_ao']}</td>
                    <td class='text-center'>{$data['tahun']}</td>
                    <td class='text-right'>" . number_format($data['target'], 0, ',', '.') . "</td>";?>
                    <td class='text-center'>
                      <button class='btn btn-warning btn-sm' data-toggle='modal' data-target='#editModal'
                              data-id='<?= $data["id_target"] ?>' 
                              data-nama='<?= $data["nama_ao"] ?>'
                              data-tahun='<?= $data["tahun"] ?>'
                              data-targetao='<?= $data["target"] ?>'>
                        Edit
                      </button> | 
                      <a href='hapus_target.php?id=<?= $data['id_target'] ?>' class='btn btn-danger btn-sm' 
                         onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                    </td>
                    
                </tr>
           <?php $no++;
        }
        $stmt->close();
    } else {
        echo "<tr><td colspan='18' class='text-center text-danger'>Gagal menyiapkan query.</td></tr>";
    }
} else {
    echo "<tr><td colspan='18' class='text-center text-warning'>Kode cabang belum dipilih.</td></tr>";
}
?>
        </tbody>
      </table>
    </div>
     <?php include "add_target.php"; ?>
  </div>
</div>
<!-- Modal Edit Target AO -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" action="update_target.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Target AO</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id_target" id="modal_id_target">
            <input type="hidden" name="id_ao" id="modal_id_target">
            <div class="form-group">
              <label>Nama AO</label>
              <input type="text" class="form-control" id="modal_nama_ao" disabled>
            </div>
            <div class="form-group">
              <label>Tahun</label>
              <input type="text" class="form-control" name="tahun" id="modal_tahun" required>
            </div>
            <div class="form-group">
              <label>Target Penyelesaian NPL</label>
              <input type="number" class="form-control" name="target" id="modal_targetao" required>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>
            
<?php include("footer.php"); ?>

<script src="datatables/js/jquery.dataTables.min.js"></script>
<script src="datatables-plugins/dataTables.bootstrap.min.js"></script>
<script src="datatables-responsive/dataTables.responsive.js"></script>
<script>
    $(document).ready(function() {
        $('#dataTables-example').DataTable({
            responsive: true
        });
    });
</script>
<script>
   $('#example').DataTable( {
    paging: false
    } );
 
    $('#example').DataTable( {
    destroy: true,
    searching: false
    } );
    function toggleFormTargetAO() {
        const form = document.getElementById("formTargetAO");
        if (form) {
            form.style.display = (form.style.display === "none" ? "block" : "none");
        }
    }

    function toggleFormUpload(id_deb) {
        const form = document.getElementById("formUploadDokumentasi" + id_deb);
        if (form) {
            form.style.display = (form.style.display === "none" ? "block" : "none");
        }
    }
$('#editModal').on('show.bs.modal', function (event) {
  const button = $(event.relatedTarget); // Tombol yang diklik
  const id = button.data('id');
  const nama = button.data('nama');
  const tahun = button.data('tahun');
  const target = button.data('targetao');

  const modal = $(this);
  modal.find('#modal_id_target').val(id);
  modal.find('#modal_nama_ao').val(nama);
  modal.find('#modal_tahun').val(tahun);
  modal.find('#modal_targetao').val(target);
});
</script>
  <script>

