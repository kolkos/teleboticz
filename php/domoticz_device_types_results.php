<?php
    require_once 'inc/config.inc.php';
    require_once 'inc/database.inc.php';
    $site = new Config();
    $db = new Database();
    $results_config = $site->prepare_query_domoticz_device_types($_POST);
    $results = $db->prepareStatementSelect($results_config['query'], $results_config['a_params']);
    echo $site->fill_results_table($results_config['fields'], $results);
?>