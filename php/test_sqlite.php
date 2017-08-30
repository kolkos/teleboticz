<?php
    require_once 'inc/database.inc.php';
    $db = new Database();

    $q = "SELECT * FROM telegram_callback_queries";
    $a_params = array (
            ':row_id' => array (
                    'value' => 12,
                    'type' => PDO::PARAM_INT
            ),
    );
    $a_params = array();
    // run query
    
    $callback_results = $db->prepareStatementSelect ( $q, $a_params );
    
    foreach($callback_results as $row){
        echo $row['id'] . " - ";
        echo $row['query_id'] . " - ";
        echo $row['msg_id'] . " - ";
        echo $row['user_id'] . " - ";
        echo $row['data'] . " - ";
        echo "<br>";
    }
?>