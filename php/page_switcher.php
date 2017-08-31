<?php
    require_once 'inc/general.inc.php';
    $general = new General();
    $page = "Home";
    if(isset($_GET['page'])){
        $page = $_GET['page'];
    }
    $general->pageSwitcher($page);
    
?>