<div id="formTargetAO" class="p-3 border rounded mt-3" style="display: none; background-color: #f9f9f9;">
<form role="form" name="form1" method="post" action="proses_add_target.php" enctype="multipart/form-data">
    <div class="row">
        <?php include("getCode/getAO.php");
              $aoOptions = $options;
        ?>
      <div class="col-lg-4">
        <div class="form-group">
            <label class="form-control-label" for="input-first-name">AO</label>
            <select class="form-control" name="id_ao">
                <option>-- Pilih AO --</option>
                <?php 
          foreach ($aoOptions as $option) {
            echo "<option value='" . htmlspecialchars($option['id_ao']) . "'>" . htmlspecialchars($option['id_ao']) . "</option>";
          }
        ?>
            </select>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
        <label class="form-control-label" for="input-first-name">Tahun</label>
            <input type="text" class="form-control" name="tahun" id="tahun" >
		</div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            <label class="form-control-label">Target</label>
            <input type="text" class="form-control format-rupiah" name="target" id="target">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <input type="submit" name="Submit" id="Submit" value="Submit" class="btn btn-primary" onClick="return valid()"/>
			<input type="reset" name="reset" id="reset" value="Reset" class="btn btn-primary">
        </div>
    </div>	
</form>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const formatInputs = document.querySelectorAll('.format-rupiah');
    formatInputs.forEach(input => {
        input.addEventListener('input', function () {
            let value = this.value.replace(/\D/g, ''); // Hapus semua karakter non-angka
            if (!value) {
                this.value = '';
                return;
            }
            this.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        });

        // Opsional: Hapus titik saat focus (agar mudah edit)
        input.addEventListener('focus', function () {
            this.value = this.value.replace(/\./g, '');
        });

        // Opsional: Tambahkan kembali titik saat blur
        input.addEventListener('blur', function () {
            let value = this.value.replace(/\D/g, '');
            if (!value) {
                this.value = '';
                return;
            }
            this.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        });
    });
});
</script>
<script>
function toggleFormDebitur() {
    const form = document.getElementById('formAddDebitur');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}
</script>