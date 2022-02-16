<?php  
$serverName = "DESKTOP-O9AQ2LD"; 

$databaseName = "NUTRITION_SYSTEM"; 

$connectionInfo = array("Database"=>$databaseName ,"CharacterSet" => "UTF-8"); 

/* Connect using SQL Server Authentication. */  
$conn = sqlsrv_connect( $serverName, $connectionInfo);  

 // Check connection
if($conn === false){
    die("ERROR: Could not connect.");
}


session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["id"]) || !isset($_SESSION["login"])){
    header("location: pages-login.php");
    exit;
}else{


}

?>