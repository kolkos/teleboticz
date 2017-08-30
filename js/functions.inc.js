function load_logfile(){
    $.ajax({
        type:"post",
        url:"php/get_log.php",
        datatype:"html",
        success:function(data)
        {
            //do something with response data
            $('#log').html(data);
            $('#log').scrollTop($('#log')[0].scrollHeight);
        }
    });
}