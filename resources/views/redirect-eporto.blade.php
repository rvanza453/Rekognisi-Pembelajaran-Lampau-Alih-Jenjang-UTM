<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Redirect - Mahasiswa E-Porto</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <style>
    .redirect-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .redirect-card {
      background: white;
      border-radius: 20px;
      padding: 3rem;
      text-align: center;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
      max-width: 500px;
      width: 90%;
    }
    
    .redirect-icon {
      font-size: 4rem;
      color: #667eea;
      margin-bottom: 1.5rem;
    }
    
    .redirect-title {
      color: #333;
      font-size: 1.8rem;
      font-weight: 600;
      margin-bottom: 1rem;
    }
    
    .redirect-message {
      color: #666;
      font-size: 1.1rem;
      margin-bottom: 2rem;
      line-height: 1.6;
    }
    
    .redirect-button {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
      color: white;
      padding: 12px 30px;
      border-radius: 50px;
      font-size: 1.1rem;
      font-weight: 500;
      text-decoration: none;
      display: inline-block;
      transition: all 0.3s ease;
      box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
    
    .redirect-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
      color: white;
      text-decoration: none;
    }
    
    .logout-link {
      color: #667eea;
      text-decoration: none;
      font-size: 0.9rem;
      margin-top: 1rem;
      display: inline-block;
    }
    
    .logout-link:hover {
      color: #764ba2;
      text-decoration: underline;
    }
  </style>
</head>

<body>

  <main>
    <div class="redirect-container">
      <div class="redirect-card">
        <div class="redirect-icon">
          <i class="bi bi-arrow-up-right-circle"></i>
        </div>
        
        <h1 class="redirect-title">Sistem E-RPL</h1>
        
        <p class="redirect-message">
          Maaf, sistem E-RPL ini hanya tersedia untuk Calon Mahasiswa Eporto. 
          Untuk mahasiswa E-Porto, silakan akses sistem yang sesuai melalui tombol di bawah ini.
        </p>
        
        <a href="https://example.com/sistem-E-Porto" target="_blank" class="redirect-button">
          <i class="bi bi-box-arrow-up-right me-2"></i>
          Akses Sistem E-Porto
        </a>
        
        <br>
        <a href="/logout" class="logout-link">
          <i class="bi bi-box-arrow-left me-1"></i>
          Kembali ke Login
        </a>
      </div>
    </div>
  </main>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html> 