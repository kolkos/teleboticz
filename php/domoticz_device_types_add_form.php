<?php
    require_once 'inc/site.inc.php';
    $site = new Site();

    $form_id = "domooticz_device_type_add_form";
    $title = "Add device type";
    $button_id = "domooticz_device_type_add_button";


    $form_elements = array(
        array(
            'id' => 'device_type_name',
            'name' => 'device_type_name',
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

    echo $site->create_form($form_id, $title, $button_id, $form_elements);
?>