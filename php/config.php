<div id="domoticz_device_types"></div>
<div id="domoticz_call_config"></div>
<script>
    $(function(){
        // Load the device types
        var source = 'php/domoticz_device_types_table.php';
        var target = '#domoticz_device_types';
        loadPageSimple(source, target);
        source = 'php/domoticz_device_types_results.php';
        target = '#domoticz_device_types_table > tbody';
        loadPageSimple(source, target);

        // load the domoticz call config 
        source = 'php/domoticz_call_config_table.php';
        target = '#domoticz_call_config';
        loadPageSimple(source, target);
        // load the results for the domoticz call config
        source = 'php/domoticz_call_config_results.php';
        target = '#domoticz_call_config_table > tbody';
        loadPageSimple(source, target);
    });
</script>