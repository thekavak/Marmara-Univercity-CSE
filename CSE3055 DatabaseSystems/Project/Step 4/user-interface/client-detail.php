
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Tables / Data - AdminSystem Bootstrap Template</title>
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

    <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="POST" action="#">
        <input type="text" name="query" placeholder="Search" title="Enter search keyword">
        <button type="submit" title="Search"><i class="bi bi-search"></i></button>
      </form>
    </div><!-- End Search Bar -->

   <?php include 'navbar.php'; ?>

  </header><!-- End Header -->
  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">
<?php include 'footer.php'; ?>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Client Detail - <?php echo $_GET['id'];?></h1>
    </div><!-- End Page Title -->

    <?php

      $sql1 = "SELECT * FROM v_detailOfClients where UserID =".$_GET['id'];
      $req =  sqlsrv_query($conn, $sql1) or die(print_r(sqlsrv_errors(),true));


         
      $sql2 = "SELECT * FROM v_detailOfClients where UserID =".$_GET['id'];
      $req2 =  sqlsrv_query($conn, $sql2) or die(print_r(sqlsrv_errors(),true));

          
      $sql3 = "  SELECT *,CaloriesNeeded-TakenCalori AS AvailableCalori FROM [v_ClientListsCalories] V INNER JOIN DietProgram D ON V.ProgramID = D.ProgramID WHERE V.IsActive = 1 AND ClientID = ".$_GET['id'];
      $req3 =  sqlsrv_query($conn, $sql3) or die(print_r(sqlsrv_errors(),true));

       $sql4 = "SELECT * FROM MealInfo";
      $req4 =  sqlsrv_query($conn, $sql4) or die(print_r(sqlsrv_errors(),true));

       $sql5 = "SELECT PM.MealInfoID, PM.ProgramMealID,MI.MealName FROM DietProgram D RIGHT JOIN ProgramMeal PM ON D.ProgramID = PM.ProgramID INNER JOIN MealInfo MI ON PM.MealInfoID = MI.MealInfoID
WHERE D.IsActive = 1 AND  D.ClientID =".$_GET['id'];
      $req5 =  sqlsrv_query($conn, $sql5) or die(print_r(sqlsrv_errors(),true));



        $sql6 = "SELECT * FROM NutrientCat";
      $req6 =  sqlsrv_query($conn, $sql6) or die(print_r(sqlsrv_errors(),true));

      
?>

       <section class="section profile">
      <div class="row">
  

        <div class="col-xl-8">

          <div class="card">
            <div class="card-body pt-3">
              <!-- Bordered Tabs -->
              <ul class="nav nav-tabs nav-tabs-bordered">

                <li class="nav-item">
                  <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
                </li>

               
              </ul>
              <div class="tab-content pt-2">

                <div class="tab-pane fade show active profile-overview" id="profile-overview">
              
                  <h5 class="card-title">Profile Details</h5>

                         <?php
                    if(sqlsrv_has_rows($req2) != 1){
                    
                  }else{
                      while($data = sqlsrv_fetch_array($req2, SQLSRV_FETCH_ASSOC)){
                        ?>
                             <div class="row">
                    <div class="col-lg-3 col-md-4 label ">Full Name</div>
                    <div class="col-lg-9 col-md-8"><?php echo $data['FullName'];?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Profession</div>
                    <div class="col-lg-9 col-md-8"><?php echo $data['Profession'];?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">UserEmail</div>
                    <div class="col-lg-9 col-md-8"><?php echo $data['UserEmail'];?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Gender</div>
                    <div class="col-lg-9 col-md-8"><?php echo $data['Gender'];?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Date of Birth</div>
                    <div class="col-lg-9 col-md-8"><?php echo $data['DateOFBirth']->format('d/m/Y h:m');?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Age</div>
                    <div class="col-lg-9 col-md-8"><?php echo $data['Age'];?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">BMI</div>
                    <div class="col-lg-9 col-md-8"><?php echo $data['Bmi'];?></div>
                  </div>

                   <div class="row">
                    <div class="col-lg-3 col-md-4 label">CaloriesNeeded</div>
                    <div class="col-lg-9 col-md-8"><?php echo $data['CaloriesNeeded'];?></div>
                  </div>
                    <div class="row">
                    <div class="col-lg-3 col-md-4 label">Medical History</div>
                    <div class="col-lg-9 col-md-8"><?php echo $data['MedicalHistory'];?></div>
                  </div>

                     <div class="row">
                    <div class="col-lg-3 col-md-4 label">Start Date</div>
                    <div class="col-lg-9 col-md-8"><?php echo $data['StartDateOfRegistration']->format('d/m/Y h:m');?></div>
                  </div>

                   <div class="row">
                    <div class="col-lg-3 col-md-4 label">End Date</div>
                    <div class="col-lg-9 col-md-8"><?php echo $data['EndDateOfRegistration']->format('d/m/Y h:m');?></div>
                  </div>

                      <?php   
                      }
                  }

              ?>

              
                </div>

            
          
              </div><!-- End Bordered Tabs -->

            </div>
          </div>

        </div>

      <div class="col-xl-4">

          <div class="card">
            <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

              <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle">
          <?php
                    if(sqlsrv_has_rows($req) != 1){
                    
                  }else{
                      while($data = sqlsrv_fetch_array($req, SQLSRV_FETCH_ASSOC)){
                        ?>
                          <h2><?php echo $data['FullName'];?></h2>
                           <h3><?php echo $data['Profession'];?></h3>
                            <h5><?php echo $data['UserEmail'];?></h5>
                      <?php   
                      }
                  }

              ?>
          
            </div>
          </div>

          
          <div class="card">
            <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
        
               <?php
                    if(sqlsrv_has_rows($req3) != 1){
                    
                  }else{
                      while($data = sqlsrv_fetch_array($req3, SQLSRV_FETCH_ASSOC)){
                        ?>
                        <div class="col-md-12 label">Calories Taken : <b><?php echo $data['TakenCalori'];?> </b></div>
                       <div class="col-md-12 label pt-4">Calories Available: <b><?php echo $data['AvailableCalori'];?> </b></div>
                         <div class="col-md-12 label pt-4">Calories Needed: <b><?php echo $data['CaloriesNeeded'];?> </b></div>
                      <?php   
                      }
                  }

              ?>   
          
            </div>
          </div>


        </div>


        </div>
        

      </div>


          <?php

      $sql11 = "SELECT * FROM ProgramMeal PM INNER JOIN MealInfo MI ON PM.MealInfoID = MI.MealInfoID
WHERE PM.ProgramID = (SELECT ProgramID FROM DietProgram WHERE ClientID =".$_GET['id']."AND IsActive = 1)";
      $req11 =  sqlsrv_query($conn, $sql11) or die(print_r(sqlsrv_errors(),true));

?>

         <div class="col-xl-12">

          <div class="card">
            <div class="card-body pt-3">
              <!-- Bordered Tabs -->
              <ul class="nav nav-tabs nav-tabs-bordered">

                <li class="nav-item">
                  <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Diet List</button>
                </li>

               
              </ul>

         

              <div class="tab-content pt-2">
<?php
   if(isset($_POST['1']) ||isset($_POST['2'])  && isset($_REQUEST['enddate'])  && isset($_REQUEST['startdate'])){
                            $id =$_GET['id'];
                        $b =isset($_POST['1'])?true:false;
                         $el =isset($_POST['2'])?true:false;
                          $l =isset($_POST['3'])?true:false;
                           $ed =isset($_POST['4'])?true:false;
                            $d = isset($_POST['5'])?true:false;
                             $n =isset($_POST['6'])?true:false;
                               $active =true;
                                  $note ='true';
                        $startdate = $_REQUEST['startdate']?$_REQUEST['startdate']:'01/01/2022';
                          $enddate = $_REQUEST['enddate']?$_REQUEST['enddate']:'01/01/2022';

                             $procedure_params = array(
                              array(&$_GET['id'], SQLSRV_PARAM_IN),
                              array(&$startdate, SQLSRV_PARAM_IN),
                              array(&$enddate, SQLSRV_PARAM_IN),
                              array(&$note, SQLSRV_PARAM_IN),
                              array(&$active, SQLSRV_PARAM_IN),
                              array(&$b, SQLSRV_PARAM_IN),
                              array(&$el, SQLSRV_PARAM_IN),
                              array(&$l, SQLSRV_PARAM_IN),
                              array(&$ed, SQLSRV_PARAM_IN),
                              array(&$d, SQLSRV_PARAM_IN),
                              array(&$n, SQLSRV_PARAM_IN)
                              
                            );

      $sql33 = "EXEC sp_CreateDietProgram @ClientID = ?, @StartDate = ?, 
                      @EndDate  = ?, @note = ?, @IsActive = ?, @B = ?, @EL = ?,
                      @L = ?, @ED = ?, @D = ?, @N = ?";
                      $stmt = sqlsrv_prepare($conn, $sql33, $procedure_params);

                      if (!sqlsrv_execute($stmt)) {
                          echo "Your code is fail!";
                          echo  $id.'d'.$d.'b'.$b;
                          die;
                      }else{
                           echo "<script>window.location.href = 'client-detail.php?id=$id' ;</script>";
                      }

                  }else if(isset($_POST['programMealID']) && isset($_POST['nutrientID']) ){
                  $id =$_GET['id'];
                          $programMealID = $_REQUEST['programMealID'];
                          $nutrientID =  $_REQUEST['nutrientID'];
                          $amount = $_REQUEST['amount'];

                               $procedure_params1 = array(
                              array(&$programMealID, SQLSRV_PARAM_IN),
                              array(&$nutrientID, SQLSRV_PARAM_IN),
                              array(&$amount, SQLSRV_PARAM_IN)
                            );

                       $sql = "EXEC sp_AddNutrientToProgramMeal @programMealID = ?, @NutrientID = ?, @Amount  = ?";
                      $stmts = sqlsrv_prepare($conn, $sql, $procedure_params1);

                      if (!sqlsrv_execute($stmts)) {
                          echo "Eklenmedi";
                          die;
                      }else{
                           echo "<script>window.location.href = 'client-detail.php?id=$id' ;</script>";
                      }

                    }else if(isset($_POST['programMealForDelete']) && isset($_POST['nutrientIDDELETE']) ){
                      $id =$_GET['id'];
                       $sqlDelete = "  DELETE FROM ProgramMealDetail WHERE ProgramMealDetail.NutrientID=".$_POST['nutrientIDDELETE']." AND ProgramMealDetail.ProgramMealID =".$_POST['programMealForDelete'];
                      $reqDelete =  sqlsrv_query($conn, $sqlDelete) or die(print_r(sqlsrv_errors(),true));
                          echo "<script>window.location.href = 'client-detail.php?id=$id' ;</script>";
                  }

?>
                <div class="tab-pane fade show active profile-overview" id="profile-overview">

                

                  <div class="card-body">
             
              <!-- Basic Modal -->
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#basicModal">
                New Program
              </button>

               <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#basicModal1">
               Add Nutrient
              </button>

                 <form action="client-detail.php?id=<?php echo $_GET['id'];?>" method="post">
                 <div class="modal fade" id="basicModal1" tabindex="-1" aria-hidden="true" style="display: none;">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Add Nutrient</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    Meals<br>

                   <select class="form-select" name="programMealID" aria-label="Default select example">
                                <?php
                    if(sqlsrv_has_rows($req5) != 1){
                    
                  }else{
                      while($dataa = sqlsrv_fetch_array($req5, SQLSRV_FETCH_ASSOC)){
                        ?>
                             
                                        <option value="<?php echo $dataa['ProgramMealID'];?>"><?php echo $dataa['MealName'];?></option>
                                    <?php   
                                    }
                                }

                            ?>
                    </select>

                      Nutrient<br>

                   <select class="form-select" name="nutrientID" aria-label="Default select example">
                                <?php
                    if(sqlsrv_has_rows($req6) != 1){
                    
                  }else{
                      while($dataa = sqlsrv_fetch_array($req6, SQLSRV_FETCH_ASSOC)){
                        ?>
                             
                                       <optgroup label="<?php echo $dataa['CatName'];?>">

                                       <?php 

                                          $sql61 = "SELECT * FROM Nutrient WHERE NutrientCatID =".$dataa['NutrientCatID'];
                                          $req61 =  sqlsrv_query($conn, $sql61) or die(print_r(sqlsrv_errors(),true));
                                      if(sqlsrv_has_rows($req61) != 1){
                                                      
                                                    }else{
                                                        while($data61 = sqlsrv_fetch_array($req61, SQLSRV_FETCH_ASSOC)){
                                                  ?>
                                                    <option value=" <?php echo $data61['NutrientID'];?>"> <?php echo $data61['Name'].'  '.$data61['MeasurementUnit'].' / '.$data61['EnergyKcal'];?> </option>
                                                
                                                      <?php   
                                                      }
                                                  }

                                              ?>

                                          </optgroup>
                                    <?php   
                                    }
                                }

                            ?>
                    </select>


                  
          <br>
      <div class="row mb-3">
                  <label for="inputDate" class="col-sm-2 col-form-label">Amount</label>
                  <div class="col-sm-8">
                    <input type="number" name="amount" class="form-control">
                  </div>
                </div>

            
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary">Add</button>
                    </div>
                  </div>
                </div>
              </div><!-- End Basic Modal-->

            </div>
                </form>
				
 <form action="client-detail.php?id=<?php echo $_GET['id'];?>" method="post">
              <div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true" style="display: none;">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">New Program</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    Meals<br>

                       <?php
                    if(sqlsrv_has_rows($req4) != 1){
                    
                  }else{
                      while($data112 = sqlsrv_fetch_array($req4, SQLSRV_FETCH_ASSOC)){
                        ?>
                      <div class="col-sm-10 offset-sm-2">
                          <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="<?php echo $data112['MealInfoID'];?>" id="<?php echo $data112['MealInfoID'];?>">
                            <label class="form-check-label" for="gridCheck1">
                               <?php echo $data112['MealName'].'-'.$data112['TimeRange'];?>
                            </label>
                          </div>
                        </div>
                      <?php   
                      }
                  }

              ?>
          <br>
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

                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                  </div>
                </div>
              </div><!-- End Basic Modal-->

            </div>
                </form>

              <div class="row ">
              
                   <?php
                    if(sqlsrv_has_rows($req11) != 1){
                    
                  }else{
                      while($data = sqlsrv_fetch_array($req11, SQLSRV_FETCH_ASSOC)){

                          $sqlDetail = "SELECT N.NutrientID,N.Name, PMD.* FROM ProgramMealDetail PMD INNER JOIN Nutrient N 
                          ON PMD.NutrientID = N.NutrientID WHERE ProgramMealID =".$data['ProgramMealID']."";
                            $reqdetail =  sqlsrv_query($conn, $sqlDetail) or die(print_r(sqlsrv_errors(),true));

                        ?>
                            <h5 class="card-title"><?php echo $data['MealName'].'-'.$data['TimeRange'];?></h5>

                          <?php
                          if(sqlsrv_has_rows($reqdetail) != 1){

                          }else{
                          while($dataDetail = sqlsrv_fetch_array($reqdetail, SQLSRV_FETCH_ASSOC)){
                            ?>
                          <div class="row pt-1">
                                  <div class="col-lg-3 col-md-4 label"><?php echo $dataDetail['Name'];?></div>
                                  <div class="col-lg-3 col-md-4 label"><?php echo $dataDetail['AmountOfPorsion'];?>gr</div>
                                    <div class="col-lg-3 col-md-4 label"><?php echo $dataDetail['EnergyKcal'];?>cal</div>
                                      <div class="col-lg-1 col-md-4 label ">
                                     <form action="client-detail.php?id=<?php echo $_GET['id'];?>" method="post">
                                        <input type="hidden" name="programMealForDelete" value="<?php echo $data['ProgramMealID']; ?>">
                                            <input type="hidden" name="nutrientIDDELETE" value="<?php echo $dataDetail['NutrientID']; ?>">
                                        <td>                                           <button type="submit" class="btn btn-secondary btn-sm"><i class="bi bi-trash"></i></button></td>
                                        </form>


                                        </div>
                                   
                                </div>

                      <?php    } 
                        }
                          ?>

                            
                      <?php   
                      }
                  }

              ?>
          

                


              
                </div>

         
                

              </div><!-- End Bordered Tabs -->

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