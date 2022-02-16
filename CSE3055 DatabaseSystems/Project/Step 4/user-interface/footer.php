
    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link " href="index.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

    <?php  if($_SESSION["login"] == 'N') {
              ?>
        

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person-circle"></i><span>Clients</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="clients.php">
              <span>List</span>
            </a>
          </li>
          <li>
            <a href="clients-add.php">
              <span>Add</span>
            </a>
          </li>
       
        </ul>
      </li><!-- End Components Nav -->

    
         <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#components-nav1" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person"></i><span>Nutritionists</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav1" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="nutritionist.php">
              <span>List</span>
            </a>
          </li>
          <li>
            <a href="nutritionist-add.php">
            <span>Add</span>
            </a>
          </li>
       
        </ul>
      </li><!-- End Components Nav -->

       <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#components-nav2" data-bs-toggle="collapse" href="#">
          <i class="bi bi-egg-fried"></i><span>Nutrients</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav2" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li>
            <a href="categories.php">
            <span>Categories</span>
            </a>
          </li>
       
          <li>
            <a href="nutrients.php">
              <span>List</span>
            </a>
          </li>
          <li>
            <a href="nutrient-add.php">
            <span>Add</span>
            </a>
          </li>
       
        </ul>
      </li><!-- End Components Nav -->
    <?php }else{
                
            }?>


      

    </ul>