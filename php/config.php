<?php
    require_once 'inc/database.inc.php';
    
    $q = "SELECT * FROM domoticz_call_config ORDER BY device_type, section ASC";
    $params = array();

    $db = new Database();

    $results = $db->prepareStatementSelect($q, $params);
    $fields = array();
    $fields['Device Type'] = 'device_type';
    $fields['Section'] = 'section';
    $fields['Key'] = 'key';
    $fields['Value'] = 'value';

    echo $db->create_results_table($results, $fields);
    
?>