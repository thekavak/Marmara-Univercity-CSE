
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Dashboard - AdminSystem Bootstrap Template</title>
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

  <!-- =======================================================
  * Template Name: AdminSystem - v2.2.0
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
        <span class="d-none d-lg-block">AdminSystem</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

<?php include 'navbar.php'; ?>

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">
<?php include 'footer.php'; ?>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">

        <!-- Left side columns -->
        <div class="col-lg-8">
          <div class="row">
<?php
$sql1 = "SELECT UserType,Count(*) as total FROM [User] Group by UserType";
$req =  sqlsrv_query($conn, $sql1) or die(print_r(sqlsrv_errors(),true));

  if(sqlsrv_has_rows($req) != 1){
     
  }else{
      while($data = sqlsrv_fetch_array($req, SQLSRV_FETCH_ASSOC)){
        ?>
           <div class="col-xxl-6 col-md-6">
             
              <div class="card info-card" <?php if($data['UserType'] == 'N') echo "sales-card"; else {echo "renevue-card"; }?>>
                <div class="card-body">
                  <h5 class="card-title"><?php if($data['UserType'] == 'N') echo "Diyetistenler"; else {echo "Danışanlar"; }?></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="<?php if($data['UserType'] == 'N') echo "bi bi-person"; else {echo "bi bi-person-circle"; }?>"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?php echo $data['total']?></h6>
                      </div>
                  </div>
                </div>

              </div>
            </div><!-- End Sales Card -->
       <?php   
      }
  }
?>
      
         



            <!-- Recent Sales -->
            <div class="col-12">
              <div class="card recent-sales">


                <div class="card-body">
                  <h5 class="card-title">Son Kaydolan Clientler</span></h5>

                  <table class="table table-borderless ">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">FullName</th>
                        <th scope="col">Email</th>
                        <th scope="col">Gender</th>
                        <th scope="col">Status</th>
                      </tr>
                    </thead>
                    <tbody>

                    <?php
$sql2 = "SELECT top 10 U.* ,C.IsActive FROM [User] U INNER JOIN Client C ON U.UserID=C.ClientID where U.UserType='C' order by U.UserID desc";
$req =  sqlsrv_query($conn, $sql2) or die(print_r(sqlsrv_errors(),true));

  if(sqlsrv_has_rows($req) != 1){
     
  }else{
      while($data = sqlsrv_fetch_array($req, SQLSRV_FETCH_ASSOC)){
        ?>
                <tr>
                        <th scope="row"><a href="#"><?php echo $data['UserID'];?> </a></th>
                        <td><?php echo $data['FullName'];?></td>
                        <td><?php echo $data['UserEmail'];?></td>
                        <td><?php echo $data['Gender'];?></td>
                        <td><span class="<?php if($data['IsActive'] == TRUE) echo "badge bg-success"; else {echo "badge bg-warning"; }?>"><?php echo $data['IsActive'];?></span></td>
                      </tr>
       <?php   
      }
  }
?>
     
                    </tbody>
                  </table>

                </div>

              </div>
            </div><!-- End Recent Sales -->



          </div>
        </div><!-- End Left side columns -->

        <!-- Right side columns -->
        <div class="col-lg-4">

          <!-- Recent Activity -->
          <div class="card">
         

            <div class="card-body">
              <h5 class="card-title">Recent FeedBacks <span>| Last 10</span></h5>

              <div class="activity">

              
                    <?php
$sql3 = "SELECT top 10 d.FeedBackDate,c.FullName from DailyFeedback  d inner join [User] c on d.ClientID=c.UserID order by FeedBackDate ";
$req =  sqlsrv_query($conn, $sql3) or die(print_r(sqlsrv_errors(),true));

  if(sqlsrv_has_rows($req) != 1){
     
  }else{
      while($data = sqlsrv_fetch_array($req, SQLSRV_FETCH_ASSOC)){
        ?>
                
                <div class="activity-item d-flex">
                  <div class="activite-label">  <?php echo $data['FeedBackDate']->format('d/m/Y h:m'); ?> </div>
                  <i class='bi bi-circle-fill activity-badge text-muted align-self-start'></i>
                  <div class="activity-content">
                   <?php echo $data['FullName']; ?>
                  </div>
                </div><!-- End activity item-->
   <?php   
      }
  }
?>
              </div>

            </div>
          </div><!-- End Recent Activity -->

   



        </div><!-- End Right side columns -->

      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>AdminSystem</span></strong>. All Rights Reserved
    </div>
   
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.min.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>