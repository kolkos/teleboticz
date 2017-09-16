<?php
    require_once 'inc/config.inc.php';
    $site = new Config();

    $form_name = "domoticz_device_type_add";
    $title = "Add device type";
    $file = "php/domoticz_device_types_add_handler.php";


    $form_elements = array(
        array(
            'id' => 'device_type_name',
            'name' => 'device_type_name',
            'required' => TRUE,
            'type' => 'text',
            'label' => 'Name'
        ),
        array(
            'id' => 'device_type_description',
            'name' => 'device_type_description',
            'type' => 'textarea',
            'label' => 'Description',
        ),
    );

    echo $site->create_form($form_name, $title, $form_elements, $file);
?>
