<form action="" method="post">
    <input type="text" name="veld1" value="gevuld"/>
    <input type="text" name="veld2"/>
    <input type="text" name="veld3"/>
    <input type="text" name="veld4" value="gevuld"/>
    <input type="submit" name="submit"/>
</form>
<?php
    if(isset($_POST['submit'])){
        print_r($_POST);
    }
?>