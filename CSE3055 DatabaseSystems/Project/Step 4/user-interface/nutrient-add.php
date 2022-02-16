<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Forms / Elements - AdminSystem Bootstrap Template</title>
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


   <?php  include 'navbar.php'; ?>
  </header><!-- End Header -->
  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">
<?php include 'footer.php'; ?>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Add New Nutrient</h1>
   
    </div><!-- End Page Title -->

       <?php

         $sql1 = "SELECT DISTINCT VitaminName FROM Vitamin";
        $req1 =  sqlsrv_query($conn, $sql1) or die(print_r(sqlsrv_errors(),true));


        $sql2 = "SELECT * FROM [NutrientCat]";
        $req2 =  sqlsrv_query($conn, $sql2) or die(print_r(sqlsrv_errors(),true));

?>


    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title"></h5>

              <?php
              if( isset($_REQUEST['name'])  && isset($_REQUEST['catID']))
                  {
                  
                         
                        $name = $_REQUEST['name']? $_REQUEST['name'] : '';
                        $CatID = $_REQUEST['catID']? $_REQUEST['catID'] : 2;
                         $carbon = $_REQUEST['carbon']? $_REQUEST['carbon'] : 0;
                            $protein = $_REQUEST['protein']? $_REQUEST['protein'] : 0;
                               $fat = $_REQUEST['fat']? $_REQUEST['fat'] : 0;
                                  $fiber = $_REQUEST['fiber']? $_REQUEST['fiber'] : 0;
                                    $unit = $_REQUEST['unit']? $_REQUEST['unit'] : 0;
                                    $kcal = $_REQUEST['kcal']? $_REQUEST['kcal'] : 0;
                                     $List ='';
                                    if( isset($_REQUEST['vitamins']) )
                                    {  
                                      $List = implode(',',$_REQUEST['vitamins']);
                                    }
                   
                            $procedure_params = array(
                              array(&$name, SQLSRV_PARAM_IN),
                              array(&$carbon, SQLSRV_PARAM_IN),
                              array(&$protein, SQLSRV_PARAM_IN),
                              array(&$fat, SQLSRV_PARAM_IN),
                              array(&$fiber, SQLSRV_PARAM_IN),
                              array(&$unit, SQLSRV_PARAM_IN),
                              array(&$kcal, SQLSRV_PARAM_IN),
                              array(&$CatID, SQLSRV_PARAM_IN),
                              array(&$List, SQLSRV_PARAM_IN)                           
                            );
    
                      $sql3 = "EXEC sp_AddNutrientWithVitamins @Name = ?, 
                      @CarbonHydrates = ?, @Protein = ?, @Fat = ?, @Fiber = ?, @Unit = ?,
                      @Kcal=?, @CatID = ?, @VitaminList = ?";
                      $stmt = sqlsrv_prepare($conn, $sql3, $procedure_params);

                      if (!sqlsrv_execute($stmt)) {
                          $message = "wrong process";
                          echo "<script type='text/javascript'>alert('$message');</script>";
                        
                      }else{
                           echo '<script>window.location.href = "nutrients.php";</script>';
                      }

                  }
              ?>

              <!-- General Form Elements -->
                 <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Name</label>
                  <div class="col-sm-8">
                    <input type="text" name="name" class="form-control">
                  </div>
                </div>

                 <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">Nutrient Cat</label>
                  <div class="col-sm-8">
                    <select class="form-select" name="catID" aria-label="Default select example">
                                  <?php

                                if(sqlsrv_has_rows($req2) != 1){
                                  
                                }else{
                                    while($data = sqlsrv_fetch_array($req2, SQLSRV_FETCH_ASSOC)){
                                      ?>
                                        <option value="<?php echo $data['NutrientCatID'];?>"><?php echo $data['CatName'];?></option>
                                    <?php   
                                    }
                                }

                            ?>

                    </select>
                  </div>
                </div>

              <div class="row mb-3">
                  <label for="inputNumber" class="col-sm-2 col-form-label">CarbonHydrates</label>
                  <div class="col-sm-8">
                    <input type="number"  name="carbon" class="form-control" >
                  </div>
                </div>
                
              <div class="row mb-3">
                  <label for="inputNumber" class="col-sm-2 col-form-label">Protein</label>
                  <div class="col-sm-8">
                    <input type="number"  name="protein" class="form-control" >
                  </div>
                </div>
                  <div class="row mb-3">
                  <label for="inputNumber" class="col-sm-2 col-form-label">Fat</label>
                  <div class="col-sm-8">
                    <input type="number"  name="fat" class="form-control" >
                  </div>
                </div>

                  <div class="row mb-3">
                  <label for="inputNumber" class="col-sm-2 col-form-label">Fiber</label>
                  <div class="col-sm-8">
                    <input type="number"  name="fiber" class="form-control" >
                  </div>
                </div>

                  <div class="row mb-3">
                  <label for="inputNumber" class="col-sm-2 col-form-label">Unit </label>
                  <div class="col-sm-8">
                    <input type="number"  name="unit" class="form-control" >
                  </div>
                </div>
               <div class="row mb-3">
                  <label for="inputNumber" class="col-sm-2 col-form-label">Kcal </label>
                  <div class="col-sm-8">
                    <input type="number"  name="kcal" class="form-control" >
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">Vitamin List</label>
                  <div class="col-sm-10">
                    <select class="form-select" name="vitamins[]" multiple="multiple" aria-label="multiple select example">
                      
                       <?php

                                if(sqlsrv_has_rows($req1) != 1){
                                  
                                }else{
                                    while($data = sqlsrv_fetch_array($req1, SQLSRV_FETCH_ASSOC)){
                                      ?>
                                        <option value="<?php echo $data['VitaminName'];?>"><?php echo $data['VitaminName'];?></option>
                                    <?php   
                                    }
                                }

                            ?>

                    </select>
                  </div>
                </div>

               
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">Submit Button</label>
                  <div class="col-sm-10">
                    <button type="submit" class="btn btn-primary">Add Nutrient</button>
                  </div>
                </div>
                


              </form><!-- End General Form Elements -->

            </div>
          </div>

        </div>

   
      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>AdminSystem</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      <!-- All the links in the footer should remain intact. -->
      <!-- You can delete the links only if you purchased the pro version. -->
      <!-- Licensing information: https://bootstrapmade.com/license/ -->
      <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
      Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
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