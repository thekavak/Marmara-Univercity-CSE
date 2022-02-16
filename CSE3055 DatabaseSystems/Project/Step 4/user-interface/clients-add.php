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
      <h1>Add New Client</h1>
   
    </div><!-- End Page Title -->

       <?php

        $sql1 = "SELECT * FROM [v_detailOfNutritionist]";
        $req =  sqlsrv_query($conn, $sql1) or die(print_r(sqlsrv_errors(),true));

        $sql2 = "SELECT * FROM [DailyActivityLevel]";
        $req2 =  sqlsrv_query($conn, $sql2) or die(print_r(sqlsrv_errors(),true));

?>


    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title"></h5>

              <?php
              if(isset($_REQUEST['nutritionist']) && isset($_REQUEST['fullname'])  && isset($_REQUEST['number']))
                  {
                          $nutritionist = $_REQUEST['nutritionist'];
                          $fullname = $_REQUEST['fullname']? $_REQUEST['fullname'] : '';
                          $gender = $_REQUEST['gender'] ?  $_REQUEST['gender']:'M';
                          $number = $_REQUEST['number']?  $_REQUEST['number']:0;
                          $password = $_REQUEST['password']? $_REQUEST['password']:'';
                          $email = $_REQUEST['email'] ? $_REQUEST['email'] :'';
                          $profession = $_REQUEST['profession'] ?$_REQUEST['profession'] : '' ;
                          $weight = $_REQUEST['weight']?$_REQUEST['weight']:75;
                          $height = $_REQUEST['height']?$_REQUEST['height']:165;
                          $tweight = $_REQUEST['tweight']?$_REQUEST['tweight']:75;
                          $fatrate = $_REQUEST['fatrate']?$_REQUEST['fatrate']:0;
                          $birthdate = $_REQUEST['birthdate']? $_REQUEST['birthdate']:'01/01/1999';
                          $startdate = $_REQUEST['startdate']?$_REQUEST['startdate']:'01/01/1999';
                          $enddate = $_REQUEST['enddate']?$_REQUEST['enddate']:'01/01/1999';
                          $history = $_REQUEST['history']? $_REQUEST['history']:'';
                          $active = $_REQUEST['active'];
                          $activityLevel = $_REQUEST['activityLevel'];                               
                            $procedure_params = array(
                              array(&$nutritionist, SQLSRV_PARAM_IN),
                              array(&$fullname, SQLSRV_PARAM_IN),
                              array(&$gender, SQLSRV_PARAM_IN),
                              array(&$number, SQLSRV_PARAM_IN),
                              array(&$password, SQLSRV_PARAM_IN),
                              array(&$email, SQLSRV_PARAM_IN),
                              array(&$profession, SQLSRV_PARAM_IN),
                              array(&$weight, SQLSRV_PARAM_IN),
                              array(&$height, SQLSRV_PARAM_IN),
                              array(&$tweight, SQLSRV_PARAM_IN),
                              array(&$fatrate, SQLSRV_PARAM_IN),
                              array(&$birthdate, SQLSRV_PARAM_IN),
                              array(&$startdate, SQLSRV_PARAM_IN),
                              array(&$enddate, SQLSRV_PARAM_IN),
                              array(&$history, SQLSRV_PARAM_IN),
                              array(&$active, SQLSRV_PARAM_IN),
                              array(&$activityLevel, SQLSRV_PARAM_IN)
                            
                            
                            );
    
                      $sql3 = "EXEC sp_AddNewClient @NutritionistID = ?, @FullName = ?, 
                      @Gender = ?, @PhoneNumber = ?, @Password = ?, @UserEmail = ?, @Profession = ?,
                      @weight = ?, @height = ?, @targetWeight = ?, @fatRate = ?, @birthDate = ?,
                       @startDate = ?, @endDate = ?, @history = ?, @isActive = ?, @dailyActivityLevel = ?";
                      $stmt = sqlsrv_prepare($conn, $sql3, $procedure_params);

                      if (!sqlsrv_execute($stmt)) {
                          echo "Your code is fail!";
                          die;
                      }else{
                           echo '<script>window.location.href = "clients.php";</script>';
                      }

                  }
              ?>

              <!-- General Form Elements -->
                 <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

               <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">Nutritionist</label>
                  <div class="col-sm-8">
                    <select class="form-select" name="nutritionist" aria-label="Default select example">
                            <?php

                                if(sqlsrv_has_rows($req) != 1){
                                  
                                }else{
                                    while($data = sqlsrv_fetch_array($req, SQLSRV_FETCH_ASSOC)){
                                      ?>
                                        <option value="<?php echo $data['UserID'];?> "><?php echo $data['FullName'];?></option>
                                    <?php   
                                    }
                                }

                            ?>

                     
                    
                    </select>
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">FullName</label>
                  <div class="col-sm-8">
                    <input type="text" name="fullname" class="form-control">
                  </div>
                </div>

                      <fieldset class="row mb-3">
                  <legend class="col-form-label col-sm-2 pt-0">Gender</legend>
                  <div class="col-sm-8">
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="gender" id="M" value="M" checked>
                      <label class="form-check-label" for="gridRadios1">
                        Male
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="gender" id="F" value="F">
                      <label class="form-check-label" for="gridRadios2">
                       Female
                      </label>
                    </div>
                   
                  </div>
                </fieldset>

              <div class="row mb-3">
                  <label for="inputNumber" class="col-sm-2 col-form-label">Number</label>
                  <div class="col-sm-8">
                    <input type="tel"  name="number" class="form-control" pattern="[0-9]{3}-[0-9]{2}-[0-9]{3}">
                  </div>
                </div>

               <div class="row mb-3">
                  <label for="inputPassword" class="col-sm-2 col-form-label">Password</label>
                  <div class="col-sm-8">
                    <input type="password"  name="password" class="form-control">
                  </div>
                </div>


                <div class="row mb-3">
                  <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                  <div class="col-sm-8">
                    <input type="email"  name="email" class="form-control">
                  </div>
                </div>
            
                     <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Profession</label>
                  <div class="col-sm-8">
                    <input type="text"  name="profession" class="form-control">
                  </div>
                </div>

                    <div class="row mb-3">
                  <label for="inputNumber" class="col-sm-2 col-form-label">Weight</label>
                  <div class="col-sm-8">
                    <input type="double"   name="weight" class="form-control">
                  </div>
                  </div>

           
                      <div class="row mb-3">
                  <label for="inputNumber" class="col-sm-2 col-form-label">Height</label>
                  <div class="col-sm-8">
                    <input type="double"  name="height" class="form-control">
                  </div>
                  </div>

                     <div class="row mb-3">
                  <label for="inputNumber" class="col-sm-2 col-form-label">Target Weight</label>
                  <div class="col-sm-8">
                    <input type="double"  name="tweight" class="form-control">
                  </div>
                  </div>

                  <div class="row mb-3">
                  <label for="inputNumber" class="col-sm-2 col-form-label">Fat Rate</label>
                  <div class="col-sm-8">
                    <input type="double"  name="fatrate" class="form-control">
                  </div>
                  </div>


                      <div class="row mb-3">
                  <label for="inputDate" class="col-sm-2 col-form-label">BirthDate</label>
                  <div class="col-sm-8">
                    <input type="date"  name="birthdate" class="form-control">
                  </div>
                </div>
              <div class="row mb-3">
                  <label for="inputDate" class="col-sm-2 col-form-label">Start Date</label>
                  <div class="col-sm-8">
                    <input type="date" name="startdate" class="form-control">
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="inputDate" class="col-sm-2 col-form-label">End Date</label>
                  <div class="col-sm-8">
                    <input type="date" name="enddate" class="form-control">
                  </div>
                </div>


                  <div class="row mb-3">
                  <label for="inputPassword" class="col-sm-2 col-form-label">History</label>
                  <div class="col-sm-8">
                    <textarea class="form-control" name="history" style="height: 100px"></textarea>
                  </div>
                </div>

                            <fieldset class="row mb-3">
                  <legend class="col-form-label col-sm-2 pt-0">Active</legend>
                  <div class="col-sm-8">
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="active" id="1" value="1" checked>
                      <label class="form-check-label" for="gridRadios1">
                        1
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="active" id="0" value="0">
                      <label class="form-check-label" for="gridRadios2">
                       0
                      </label>
                    </div>
                   
                  </div>
                </fieldset>

                  <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">DailyActiviteLevel</label>
                  <div class="col-sm-8">
                    <select class="form-select" name="activityLevel" aria-label="Default select example">
                                  <?php

                                if(sqlsrv_has_rows($req2) != 1){
                                  
                                }else{
                                    while($data = sqlsrv_fetch_array($req2, SQLSRV_FETCH_ASSOC)){
                                      ?>
                                        <option value="<?php echo $data['LevelID'];?>"><?php echo $data['LevelFactor'].'-'. $data['LevelName'];?></option>
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
                    <button type="submit" class="btn btn-primary">Add Client</button>
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