<?php
    $bulanQuery = "SELECT DISTINCT DATE_FORMAT(tgl_data, '%Y-%m') AS bulan 
                   FROM debitur_npl 
                   WHERE kode_cabang = ? 
                   ORDER BY bulan DESC";
    $stmtBulan = $mysqli->prepare($bulanQuery);
    $stmtBulan->bind_param("s", $kode_cabang);
    $stmtBulan->execute();
    $bulanResult = $stmtBulan->get_result();

    $bulanOptions = [];
    while ($row = $bulanResult->fetch_assoc()) {
        $bulanOptions[] = $row['bulan'];
    }

    $selectedBulan = $_GET['bulan'] ?? '';
    ?>

    <!-- Filter Bulan -->
  <form method="get" class="form-inline mb-3">
    <input type="hidden" name="kode_cabang" value="<?= htmlspecialchars($kode_cabang) ?>">
    <label for="bulan" class="mr-2 font-weight-bold">Pilih Bulan:</label>
    <select name="bulan" id="bulan" class="form-control mr-3" style="min-width: 220px;" onchange="this.form.submit()" required>
        <option value="">-- Pilih Bulan --</option>
            <?php foreach ($bulanOptions as $bulan): ?>
            <option value="<?= $bulan ?>" <?= ($selectedBulan == $bulan) ? 'selected' : '' ?>>
            <?= date("F Y", strtotime($bulan . '-01')) ?>
            </option>
    <?php endforeach; ?>
  </select>
</form>
