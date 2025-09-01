<?php
$kode_cabang = isset($_SESSION['kode_cabang']) ? $_SESSION['kode_cabang'] : '';
$id_ao = isset($_SESSION['id_ao']) ? $_SESSION['id_ao'] : '';
$upload_time = new DateTime();
?>
<div id="formAddDebitur" class="p-3 border rounded mt-3" style="display: none; background-color: #f9f9f9;">
<form role="form" name="form1" method="post" action="proses_add_deb.php" enctype="multipart/form-data">
    <div class="row">
        <input type="hidden" class="form-control" name="kode_cabang" id="kode_cabang" value="<?php echo $kode_cabang; ?>" readonly>
        <input type="hidden" class="form-control" name="id_ao" id="id_ao" value="<?php echo $id_ao; ?>" readonly>
        <input type="hidden" class="form-control" name="tgl_data" id="tgl_data" value="<?php $upload_time = new DateTime(); 
            echo $upload_time->format('Y-m-d H:i:s');?>" readonly>
    <div class="col-lg-6">
        <div class="form-group">
        <label class="form-control-label" for="input-first-name">No. Rekening</label>
            <input type="number" class="form-control" name="norek" id="norek" required>
		</div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label class="form-control-label" for="input-first-name">Kolektibilitas</label>
            <select class="form-control" name="kolek">
                <option>Kurang Lancar (3)</option>
                <option>Diragukan (4)</option>
                <option>Macet (5)</option>
                <option>Hapus Buku</option>
            </select>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
        <label class="form-control-label" for="input-first-name">Nama Calon Debitur</label>
            <input type="text" class="form-control" name="nama_debitur" id="nama_debitur" required>
		</div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
        <label class="form-control-label" for="input-first-name">Alamat</label>
            <textarea type="text" class="form-control" name="alamat_npl" id="alamat_npl" required></textarea>
		</div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            <label class="form-control-label">Plafon</label>
            <input type="text" class="form-control format-rupiah" name="plafon_npl" id="plafon_npl" required>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            <label class="form-control-label">Bakidebet</label>
            <input type="text" class="form-control format-rupiah" name="bd_npl" id="bd_npl" required>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            <label class="form-control-label">CKPN</label>
            <input type="text" class="form-control format-rupiah" name="ckpn" id="ckpn" required>
        </div>
    </div>
        <div class="col-lg-12">
        <div class="form-group">
        <label class="form-control-label" for="input-first-name">Penyebab Permasalahan</label>
            <textarea type="text" class="form-control" name="penyebab_masalah" id="penyebab_masalah" required></textarea>
		</div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label class="form-control-label" for="input-first-name">Strategi Penyelesaian</label>
            <select class="form-control" name="strategi_penyelesaian" required>
                <option>Restrukturisasi Kredit</option>
                <option>Novasi</option>
                <option>Subrogasi</option>
                <option>Penjualan Aset</option>
                <option>AYDA Penyelesaian</option>
                <option>Gugatan Hukum Sederhana</option>
                <option>Pengadilan Niaga</option>
                <option>Lelang PKPU</option>
                <option>Pembayaran Bertahap/Menyicil</option>
                <option>Lainnya</option>
            </select>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
        <label class="form-control-label" for="input-first-name">Alasan Strategi Penyelesaian</label>
            <textarea type="text" class="form-control" name="alasan_strategi" id="alasan_strategi" required></textarea>
		</div>
    </div>
    <div class="table-responsive">
        <table class="table" border="0">
            <tbody>
                <tr>
                    <td>Debitur dikonfirmasi baik melalui telpon/WA atau kunjungan langsung</td>
                    <td>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" id="satu" name="satu" value="1" checked required>
                            <i class='far fa-thumbs-up' style='font-size:24px;color:green'></i>
                            <label class="form-check-label" for="radio1"></label>
                        </div>
                    </td>
                    <td>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" id="satu" name="satu" value="0" checked required>
                            <i class='far fa-thumbs-down' style='font-size:24px;color:red'></i>
                            <label class="form-check-label" for="radio1"></label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Debitur sudah dikunjungi namun alamat tidak sesuai/belum ditemukan tempat domisili yang baru</td>
                    <td>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" id="dua" name="dua" value="1" checked required>
                            <i class='far fa-thumbs-up' style='font-size:24px;color:green'></i>
                            <label class="form-check-label" for="radio1"></label>
                        </div>
                    </td>
                    <td>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" id="dua" name="dua" value="0" checked required>
                            <i class='far fa-thumbs-down' style='font-size:24px;color:red'></i>
                            <label class="form-check-label" for="radio1"></label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Debitur sudah terkonfirmasi namun belum ada kemampuan janji untuk membayar</td>
                    <td>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" id="tiga" name="tiga" value="1" checked required>
                            <i class='far fa-thumbs-up' style='font-size:24px;color:green'></i>
                            <label class="form-check-label" for="radio1"></label>
                        </div>
                    </td>
                    <td>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" id="tiga" name="tiga" value="0" checked required>
                            <i class='far fa-thumbs-down' style='font-size:24px;color:red'></i>
                            <label class="form-check-label" for="radio1"></label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Debitur sudah terkonfirmasi sudah ada kemampuan janji untuk membayar</td>
                    <td>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" id="empat" name="empat" value="1" checked required>
                            <i class='far fa-thumbs-up' style='font-size:24px;color:green'></i>
                            <label class="form-check-label" for="radio1"></label>
                        </div>
                    </td>
                    <td>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" id="empat" name="empat" value="0" checked required>
                            <i class='far fa-thumbs-down' style='font-size:24px;color:red'></i>
                            <label class="form-check-label" for="radio1"></label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Realisasi pembayaran dari debitur</td>
                    <td>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" id="lima" name="lima" value="1" checked required>
                            <i class='far fa-thumbs-up' style='font-size:24px;color:green'></i>
                            <label class="form-check-label" for="radio1"></label>
                        </div>
                    </td>
                    <td>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" id="lima" name="lima" value="0" checked required>
                            <i class='far fa-thumbs-down' style='font-size:24px;color:red'></i>
                            <label class="form-check-label" for="radio1"></label>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label class="form-control-label">Bayar Pokok</label>
            <input type="text" class="form-control format-rupiah" name="bayar_pokok" id="bayar_pokok" required>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label class="form-control-label">Bayar Bunga</label>
            <input type="text" class="form-control format-rupiah" name="bayar_bunga" id="bayar_bunga" required>
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