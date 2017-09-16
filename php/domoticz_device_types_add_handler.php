<?php 
    require_once 'inc/config.inc.php';
    $site = new Config();
    
    $results_config = $site->prepare_query_domoticz_device_types(null);
    
    $q = "INSERT INTO domoticz_device_types (name, description) VALUES (:name, :description);";
    
    $a_params = array(
        ':name' => array(
            'value' => $_POST['device_type_name'],
            'type' => PDO::PARAM_STR
        ),
        ':description' => array(
            'value' => $_POST['device_type_description'],
            'type' => PDO::PARAM_STR
        ),
    );
    
    $results_file = $results_config['results_file'];
    $results_target_id = '#' . $results_config['table_id'] . " > tbody";
    
    
    
    echo $site->handle_form_simple($q, $a_params, $results_file, $results_target_id);
?>