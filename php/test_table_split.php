<?php
    $fields = array(
        'Device Type' => array(
            'column' => "domoticz_device_types.name",
            'type' => "text",
            'filter' => TRUE
        ),
        'Edit' => array(
            'column' => "domoticz_call_config.device_type",
            'type' => "icon",
            'icon' => "img/edit.png",
            'function' => "edit_domoticz_call_config_device",
            'filter' => FALSE
        ),
        'Delete' => array(
            'column' => "domoticz_call_config.device_type",
            'type' => "icon",
            'icon' => "img/trash.png",
            'function' => "delete_domoticz_call_config_device",
            'filter' => FALSE
        ),
        'Test' => array(
            'column' => "device_type",
            'type' => "icon",
            'icon' => "img/trash.png",
            'function' => "delete_domoticz_call_config_device",
            'filter' => FALSE
        ),
    );

    // loop fields
    foreach($fields as $key => $value){
        $column_name_array = explode('.', $value['column']);
        $column_name = $column_name_array[count($column_name_array) -1];
        echo $column_name . "<br />";
    }
?>