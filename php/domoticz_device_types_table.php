<h2>Device types</h2>
<p>The table below shows the registered device types.</p>
<p>
    <button type="button" id="add_device_type">Add device type</button>
</p>
<?php
    require_once 'inc/config.inc.php';
    $site = new Config();
    $results = $site->prepare_query_domoticz_device_types($_POST);
    echo $site->prepare_results_table($results['fields'], $results['form_id'], $results['table_id'], $results['filter_class']);
    echo $site->create_jquery_filter_script($results['filter_class'], $results['form_id'], $results['table_id'], $results['results_file']);
?>
<script>
    $('#add_device_type').click(function(){
        openOverlayWindow(500, 'php/domoticz_device_types_add_form.php', null);
    });
</script>