<?php
  require_once 'inc/general.inc.php';
  $page = "Home";
  if(isset($_GET['page'])){
    $page = $_GET['page'];
  }

  $general = new General();
  echo $general->build_menu($page);
?>