
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
 



            <!-- Recent Sales -->
            <div class="col-12">
              <div class="card recent-sales">


                <div class="card-body">
                  <h5 class="card-title">MY LIST</span></h5>

              
                         <div class="row ">
              
                   <?php
                   

          $sql11 = "SELECT * FROM ProgramMeal PM INNER JOIN MealInfo MI ON PM.MealInfoID = MI.MealInfoID
    WHERE PM.ProgramID = (SELECT ProgramID FROM DietProgram WHERE ClientID =".$_SESSION["id"]."AND IsActive = 1)";
          $req11 =  sqlsrv_query($conn, $sql11) or die(print_r(sqlsrv_errors(),true));


                    if(sqlsrv_has_rows($req11) != 1){
                   ?> <div class="row pt-1">
                                  <div class="col-lg-3 col-md-4 label">Aktif Listeniz Yoktur!</div>
                                  </div>   <?php
                  }else{
                      while($data = sqlsrv_fetch_array($req11, SQLSRV_FETCH_ASSOC)){

                          $sqlDetail = "SELECT N.NutrientID,N.Name, PMD.* FROM ProgramMealDetail PMD INNER JOIN Nutrient N 
                          ON PMD.NutrientID = N.NutrientID WHERE ProgramMealID =".$data['ProgramMealID']."";
                            $reqdetail =  sqlsrv_query($conn, $sqlDetail) or die(print_r(sqlsrv_errors(),true));

                        ?>
                            <h5 class="card-title"><?php echo $data['MealName'].'-'.$data['TimeRange'];?></h5>

                          <?php
                          if(sqlsrv_has_rows($reqdetail) != 1){ ?>
 <div class="row pt-1">
                                  <div class="col-lg-9 col-md-4 label">Meal girişi yapılmamıştır!</div>
                                  </div>   
                         <?php  }else{
                          while($dataDetail = sqlsrv_fetch_array($reqdetail, SQLSRV_FETCH_ASSOC)){
                          ?> 
                          <div class="row pt-1">
                                  <div class="col-lg-3 col-md-4 label"><?php echo $dataDetail['Name'];?></div>
                                  <div class="col-lg-3 col-md-4 label"><?php echo $dataDetail['AmountOfPorsion'];?>gr</div>
                                    <div class="col-lg-3 col-md-4 label"><?php echo $dataDetail['EnergyKcal'];?>cal</div>
                                
                                   
                                </div>

                      <?php    } 
                        }
                          ?>

                            
                      <?php   
                      }
                  }

              ?>
          

                


              
                </div>

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
              <h5 class="card-title">Daily Feedback</span></h5>

              <div class="activity">

                  <label class="form-label" for="customFile">Upload Photo</label>
                  <input type="file" class="form-control" id="customFile" />
               <div class="pt-4">
                 <button type="button" class="btn btn-secondary "> Send</button>
                </div>
              </div>

            </div>
          </div><!-- End Recent Activity -->

                <div class="card">
         

            <div class="card-body">
              <h5 class="card-title">Monthly Feedback</span></h5>

              <div class="activity">

                  <label class="form-label" for="customFile">FrontSide Photo</label>
                  <input type="file" class="form-control" id="customFile" />

                   <label class="form-label" for="customFile">backSide Photo</label>
                  <input type="file" class="form-control" id="customFile" />

                   <label class="form-label pt-4" for="customFile">Weight</label>
                  <input type="number" class="form-control" id="customFile" />

                   <label class="form-label  pt-4" for="customFile">Fat Rate</label>
                  <input type="number" class="form-control" id="customFile" />
          <div class="pt-4">
                 <button type="button" class="btn btn-primary "><i class="bi bi-star me-1"></i> Send</button>
                </div>
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