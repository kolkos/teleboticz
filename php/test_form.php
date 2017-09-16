<?php
    require_once 'inc/config.inc.php';
    $site = new Site();

    $form_elements = array(
        array(
            'id' => 'first_field_id',
            'name' => 'first_field_name',
            'type' => 'text',
            'label' => 'First field'
        ),
        array(
            'id' => 'second_field_id',
            'name' => 'second_field_name',
            'type' => 'text',
            'label' => 'Second field',
            'value' => 'Test value second field'
        ),
        array(
            'id' => 'third_field_id',
            'name' => 'third_field_name',
            'type' => 'textarea',
            'label' => 'Third field',
            'value' => 'Test value textarea'
        ),
    );

    echo $site->create_form('form_id', 'Titel', 'button_id', $form_elements);
?>