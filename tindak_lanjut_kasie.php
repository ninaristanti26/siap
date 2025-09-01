<?php
session_start();
include "koneksi.php";
include "atas_ao.php";

$norek = $_GET['norek'] ?? '';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<div class="main-panel">
  <div class="content-wrapper">
    <h4 class="font-weight-bold mb-4">Tindak Lanjut Kepala Seksi untuk No. Rekening: <?= htmlspecialchars($norek) ?></h4>

    <form id="formTindakLanjut" class="mb-4">
      <input type="hidden" name="norek" value="<?= htmlspecialchars($norek) ?>">
      <div class="form-group">
        <label for="catatan">Catatan Tindak Lanjut:</label>
        <textarea name="catatan" id="catatan" class="form-control" rows="4" required placeholder="Masukkan tindak lanjut..."></textarea>
      </div>
      <button type="submit" class="btn btn-success">Simpan Tindak Lanjut</button>
    </form>

    <div id="alertMsg"></div>
  </div>
</div>

<?php include("footer.php"); ?>

<script>
document.getElementById('formTindakLanjut').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = e.target;
    const data = new FormData(form);

    fetch('proses_tindak_lanjut.php', {
        method: 'POST',
        body: data
    })
    .then(res => res.text())
    .then(response => {
        const alert = document.getElementById('alertMsg');
        alert.innerHTML = `<div class="alert alert-success mt-3">${response}</div>`;
        form.reset();
    })
    .catch(err => {
        document.getElementById('alertMsg').innerHTML =
            `<div class="alert alert-danger mt-3">Gagal menyimpan: ${err}</div>`;
    });
});
</script>
