<?php

// Configuration
define('MIN_SIZE', 3);
define('MAX_SIZE', 9);
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'french_words');
define('AUTO_SCROLL', false);
define('MAX_LETTER', 26);

header('Content-Type: text/HTML; charset=utf-8');
header('Content-Encoding: none;');
session_start();
ob_end_flush();
ob_start();
set_time_limit(0);
error_reporting(-1);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
  <head>
    <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
    <meta content="utf-8" http-equiv="encoding">
    <link rel="stylesheet" href="ruzzle.css"/>
    <title>Ruzzle Finder</title>
    <!--<meta charset="utf-8"/>-->
  </head>
  
    <?php
        echo  "<table class=\"table\"> <tr>";
        for ($i=0; $i <= 15; $i++) {
            echo "<td id=\"a$i\">
            <div id=\"dl$i\" class=\"dl\" onClick=\"recup($i, 1, 'dl$i')\"> dl</div>
            <div id=\"tl$i\" class=\"tl\" onClick=\"recup($i, 2, 'tl$i')\"> tl</div>
            <div id=\"dw$i\" class=\"dw\" onClick=\"recup($i, 3, 'dw$i')\"> dw</div>
            <div id=\"tw$i\" class=\"tw\" onClick=\"recup($i, 4, 'tw$i')\"> tw</div>
            <p id=\"letter$i\"></p>
             </td> ";
            if ($i == 3 || $i == 7 || $i == 11 ){
                echo "</tr><tr>";
            }
        }
        echo "</tr></table>";
 
    ?>

    <h1>Ruzzle Finder</h1>
    <form method="post" id="form" action="algo.php">
        <input type="hidden" name="bonus" id="bonus" value=""/>
        <label for="pwd" class="titre">Votre grille</label>
        <p><input type="text" name="line1" id="pwd" maxlength="16" size="16"/><br></p>
        <p><input type="submit" name="envoyer" class="submit"/></p>
    </form>
    <script>
        var stock = [];

        function recup(case_courante, point, id_option){
            for(var i = 0; i < 15; i++){
                if (stock[i] == null){
                    stock[i] = 0;
                }
            }
            var option = document.getElementById(id_option);
             if (option.style.backgroundColor == "yellow"){
                console.log("test");
                option.style.backgroundColor = "white";
                stock[case_courante] = 0;
                console.log(stock[case_courante - 1]);
                var bonus = document.getElementById('bonus');
                bonus.value = stock;

            }
            else {
                console.log("color changement");
                option.style.backgroundColor = "yellow";
                stock[case_courante] = point;
                console.log(stock);
                var bonus = document.getElementById('bonus');
                bonus.value = stock;
            }
            var xhr_object = null;
            if (window.XMLHttpRequest){ //firefox
                xhr_object =  new XMLHttpRequest();
            }
            else if(window.ActiveXObject){//internet explorer
                xhr_object = new ActiveXObject("Microsoft.XMLHTTP");
            }
            else {// pas supporter
                alert("Votre navigateur ne supporte pas le XML");
            }
            xhr_object.onreadystatechange = function(){
                if (xhr_object.readyState == 4 && (xhr_object.status == 200 || xhr_object.status == 0)) {
                    //alert("OK");
                }
            }
            xhr_object.open("GET", "algo.php?variable1=stock", true);
            xhr_object.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr_object.send(null);
        }
        var pwd = document.getElementById('pwd');
            pwd.addEventListener('keydown', function(e){
                var value = pwd.value;
                var i = value.length;
                console.log(i);
                var td = document.getElementById('a' + (i));
                var letter = document.getElementById('letter' + (i));
                letter.style.margin = "0px";
                letter.style.height = "12px";
                letter.innerHTML = String.fromCharCode(e.keyCode);
            }, false);
    </script>

</body>
</html> 
