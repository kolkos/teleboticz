<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Teleboticz</title>
        <link rel="stylesheet" href="css/style.css">
        <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
        <script src="js/jquery-3.2.1.min.js"></script>
        <script src="js/functions.inc.js"></script>
    </head>
    <body>
        <div id="overlayBG" class="overlayBG" style="display: none;"></div>
        <div id="overlayWindow" class="overlayWindow" style="display: none;"></div>
        <div class="flex-container">
            <header>
                <h1>Teleboticz</h1>
            </header>
            <nav class="nav">
                <?php
                    require_once 'php/menu.php';
                ?>
            </nav>
            <article class="article">
                <?php
                    require_once 'php/page_switcher.php';
                ?>    
            </article>
            <footer>Copyright &copy; kolkos.nl</footer>
        </div>
        <script>
            $('#overlayBG').click(function(){
                $('#overlayBG').fadeOut('fast');
                $('#overlayWindow').fadeOut('fast');
            });
        </script>
    </body>
</html>

