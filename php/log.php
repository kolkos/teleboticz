<div id="log">Loading log file...<log>
<script>
    $(function() {
        load_logfile();
        setInterval(function()
        { 
            load_logfile();
        }, 5000);//time in milliseconds 
    });
    
</script>