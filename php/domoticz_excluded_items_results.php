<?php
    require_once 'inc/site.inc.php';
    require_once 'inc/database.inc.php';
    $site = new Site();
    $db = new Database();
    $results_config = $site->prepare_query_domoticz_excluded_items($_POST);
    $results = $db->prepareStatementSelect($results_config['query'], $results_config['a_params']);
    echo $site->fill_results_table($results_config['fields'], $results);
?>