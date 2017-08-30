<?php
    require_once 'inc/general.inc.php';
    $general = new General();
    if(isset($_SESSION['offset'])){
        $offset = $general->get_log_contents($_SESSION['offset']);
        $_SESSION['offset'] = $offset;
    }else{
        $offset = $general->get_log_contents();
        $_SESSION['offset'] = $offset;
    }
        
    
    echo $general->format_log_contents($general->log_raw);
    
?>