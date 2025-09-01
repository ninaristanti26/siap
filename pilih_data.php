<?php
    $bulanQuery = "SELECT DISTINCT DATE_FORMAT(tgl_data, '%Y-%m') AS bulan 
                   FROM debitur_npl 
                   WHERE id_ao = ? 
                   ORDER BY bulan DESC";
    $stmtBulan = $mysqli->prepare($bulanQuery);
    $stmtBulan->bind_param("s", $id_ao);
    $stmtBulan->execute();
    $bulanResult = $stmtBulan->get_result();

    $bulanOptions = [];
    while ($row = $bulanResult->fetch_assoc()) {
        $bulanOptions[] = $row['bulan'];
    }

    $selectedBulan = $_GET['bulan'] ?? '';
    ?>

  <form method="get" class="form-inline mb-3">
    <input type="hidden" name="id_ao" value="<?= htmlspecialchars($id_ao) ?>">
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