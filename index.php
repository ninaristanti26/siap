<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />
    <title>SIAP | BPR Sukabumi</title>
    <link rel="stylesheet" href="vendors/feather/feather.css" />
    <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css" />
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css" />
    <link rel="stylesheet" href="css/vertical-layout-light/style.css" />
    <link rel="shortcut icon" href="images/favicon.png" />
  </head>

  <body>
    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth px-0">
          <div class="row w-100 mx-0">
            <div class="col-lg-4 mx-auto">
              <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                <div class="brand-logo">
                  <img src="images/Logo BPR 1.png" alt="logo" />
                </div>
                <h4>Sistem Integrasi Analisis Penanganan</h4>
                <h6 class="font-weight-light">Sign in to continue.</h6>
  <?php
  include("koneksi.php");
  include("getCode/get_paket.php");
  $paketOptions = $options;

  include("getCode/get_jab.php");
  $jabatanOptions = $options;
?>

<form class="pt-3" action="proses_login" method="post" name="login">
  <!-- ID AO -->
  <div class="form-group">
    <div class="input-group input-group-alternative">
      <div class="input-group-prepend">
        <span class="input-group-text"><i class="ni ni-badge"></i></span>
      </div>
      <input class="form-control" name="id_ao" id="id_ao" placeholder="ID AO" type="text" required>
    </div>
  </div>

  <!-- Unit Kerja -->
  <div class="form-group">
    <div class="input-group input-group-alternative">
      <div class="input-group-prepend">
        <span class="input-group-text"><i class="ni ni-building"></i></span>
      </div>
      <select class="form-control" name="kode_cabang" required>
        <option value="">Pilih Unit Kerja</option>
        <?php 
          foreach ($paketOptions as $option) {
            echo "<option value='" . htmlspecialchars($option['kode_cabang']) . "'>" . htmlspecialchars($option['kode_cabang']) . "</option>";
          }
        ?>
      </select>
    </div>
  </div>

  <div class="form-group">
    <div class="input-group input-group-alternative">
      <div class="input-group-prepend">
        <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
      </div>
      <input class="form-control" name="password" id="password" placeholder="Password" type="password" required>
    </div>
  </div>

  <div class="form-group">
    <div class="input-group input-group-alternative">
      <div class="input-group-prepend">
        <span class="input-group-text"><i class="ni ni-badge"></i></span>
      </div>
      <select class="form-control" name="id_jabatan" required>
        <option value="">Pilih Jabatan</option>
        <?php 
          foreach ($jabatanOptions as $option) {
            echo "<option value='" . htmlspecialchars($option['id_jabatan']) . "'>" . htmlspecialchars($option['jabatan']) . "</option>";
          }
        ?>
      </select>
    </div>
  </div>

  <div class="mt-3">
    <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn" id="login" name="Login">
      Sign In
    </button>
  </div>

  <div class="text-center mt-4 font-weight-light">
    Don't have an account? <a href="#" class="text-primary">Contact Your Admin</a>
  </div>
</form>

</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src="../../vendors/js/vendor.bundle.base.js"></script>
    <script src="../../js/off-canvas.js"></script>
    <script src="../../js/hoverable-collapse.js"></script>
    <script src="../../js/template.js"></script>
    <script src="../../js/settings.js"></script>
    <script src="../../js/todolist.js"></script>
  </body>
</html>