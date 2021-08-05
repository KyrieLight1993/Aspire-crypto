<?php
require("sql.php");
$a="";
if (isset($_GET['a'])) {
  $a= $_GET["a"];
}
if($a=="out"){
$_SESSION["user"]="";
$user="";
msg("Logged out!");
}

?>