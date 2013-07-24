<?php
header('Content-Type: text/html; charset=UTF-8');
error_reporting(E_ALL);
set_time_limit(30);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
  <head>
    <title>Ruzzle Finder</title>
    <meta charset="utf-8"/>
    <style>


    <style>
    *{ 
    font-family: "Courier New", Courier, monospace;
      }
     .board {
     width: 250px;
    position: fixed;
    top : 0;
    right : 0;
        }
     .board span {
     display: inline-block;
     height: 50px;
     width: 50px;
     text-align: center;
     background: red;
     border: solid 3px black;
    font-size: 2.5em;
    font-weight: bold;
    border-radius: 10px;
    margin: 2px;
}
    </style>
  </head>
  <body>

<?php if (empty($_POST)) : ?>
    <h1>Ruzzle Finder</h1>
    <form method="post">
    <label for="pwd">Votre grille</label>
    <p><input type="text" name="line1" id="pwd" maxlength="4" size="4"/><br></p>
    <p><input type="text" name="line2" id="pwd" maxlength="4" size="4"/><br></p>
    <p><input type="text" name="line3" id="pwd" maxlength="4" size="4"/><br></p>
    <p><input type="text" name="line4" id="pwd" maxlength="4" size="4"/><br></p>
    <p><input type="submit" name="envoyer"/></p>
    </form>
<?php else : ?>

<?php

define('MAX_WORD', 4);

function d($string)
{
    if (is_array($string)){
        $string = 'array : ' . implode(',', $string);
    }
 //  echo $string . '<br/>';    
}

$bdd = mysql_connect("localhost", "root", "wfta325aser");
mysql_query("SET NAMES utf8");

if (!mysql_select_db("word", $bdd)) {
    die('ERROR CONNECTING TO THE DB');
}

if (
    !empty($_POST['line1']) &&
    !empty($_POST['line2']) &&
    !empty($_POST['line3']) &&
    !empty($_POST['line4'])
) {

    echo
        '<div class="board">' .
        '<span>' . implode('</span><span>', str_split($_POST['line1'])) . '</span>' .
        '<span>' . implode('</span><span>', str_split($_POST['line2'])) . '</span>' .
        '<span>' . implode('</span><span>', str_split($_POST['line3'])) . '</span>' .
        '<span>' . implode('</span><span>', str_split($_POST['line4'])) . '</span>' .
        '</div>';
    $sql = 'SELECT word FROM french';
    $table = mysql_query($sql, $bdd);
    $tableaux =  array();

    for ($count = 0; $count < MAX_WORD; $count++) {
        $tableaux[$count] = str_split($_POST['line' . ($count + 1)]);
    }

    $count = 0;
    $result = array();

    function check_around($tableaux, $mot, $nbr, $save, $pos_x, $pos_y)
    {
        // if ($nbr > 3) {
        //     die('MAX');
        // }

        $nbr += 1;
        $pos = array(
            array(1, 0), array(0, 1), array(0, -1), array(-1, 0), array(1, 1), array(1, -1), array(-1, 1), array(-1, -1)
        );
        if ($nbr == count($mot)) {
            //var_dump($mot);
            d('end of word');
            return true;
        }
        d('start');
        d($mot);
        d($mot[$nbr]);
        for ($count = 0; $count < 8; $count++) {
            d('for ' . $pos_x . $pos_y);
            d($pos[$count]);
            $interm_y = $pos_y + $pos[$count][1];
            $interm_x = $pos_x + $pos[$count][0];
            d($interm_x);
            d($interm_y);
            if (
                ($interm_x) >= 0 &&
                ($interm_x) < MAX_WORD &&
                ($interm_y) >= 0 &&
                ($interm_y) < MAX_WORD
            ) {
                d('inbound');
                if ($tableaux
                    [$pos_x + $pos[$count][0]]
                  [$pos_y + $pos[$count][1]] == $mot[$nbr]
                ) {
                    d('yay');
                    if (empty($save[$interm_x][$interm_y])) {
                        d('position test');
                        $save[$interm_x][$interm_y] = true;	
                        if (check_around($tableaux, $mot, $nbr, $save, $interm_x, $interm_y)) {
                            d('go');
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    function algo($tableaux, $mot, $nbr, $save)
    {
        for ($count2 = 0; $count2 < MAX_WORD; $count2++)  {
            for ($count3 = 0; $count3 < MAX_WORD; $count3++){
                if ($tableaux[$count2][$count3] == $mot[$nbr]){
                    $save[$count2][$count3] = true;
                    if (check_around($tableaux, $mot, $nbr, $save, $count2, $count3)){
                        return true;
                    }
                }
            }
        }
    }
    $block = 0;
    $save = array();
    while ($data = mysql_fetch_array($table)) {
        $block++;
        //      if ($block > 5)
        //        die('OK');
        //  echo $data['word'];
        $mot = str_split($data['word']);
            if (algo($tableaux, $mot, 0, $save)) {
                if ($count > 100) {
                    exit();
                }
                $result[$count] = $data['word'];
                echo $result[$count] . '<br>';
                $count++;
            }
    }
}
else {
    die('ERROR LINE EMPTY');
}
?>
<?php endif ?>
</body>
</html>

