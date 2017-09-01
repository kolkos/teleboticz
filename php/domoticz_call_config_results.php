<?php
    require_once 'inc/database.inc.php';
    $db = new Database();
    $results_config = $db->prepare_query_domoticz_call_config($_POST);
    $results = $db->prepareStatementSelect($results_config['query'], $results_config['a_params']);
    echo $db->fill_results_table($results_config['fields'], $results);
?>