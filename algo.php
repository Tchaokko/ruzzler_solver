<?php
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
//error_reporting(-1);
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Hello World!</title>
 	<meta charset ="utf-8"/>
  </head>
    <link rel="stylesheet" href="ruzzle.css"/>
  	<body>
		<?php
		// System constants
		define('DEBUG', false);
		define('MAX_WORD', 4);

		function d($string)
		{
		    if (DEBUG) {
		        if (is_array($string)){
		            $string = 'array : ' . implode(',', $string);
		        }
		        echo $string . '<br/>';
		    }
		}

		$bdd = new SQlite3('database.db');
		if (!$bdd){
		    echo 'FAIL';
		}

		if (
		    !empty($_POST['line1'])
		) {
		    $bonus = (isset($_POST['bonus'])) ? $_POST['bonus'] : NULL;
		    if ($bonus){
		    	$clean_bonus = array();
		    	$bonus_count = 0;
		    	for ($i = 0; $i < 32; $i++) {
		    		if ($bonus[$i] != ',' && $bonus_count < 16){
		    			$clean_bonus[$bonus_count] = $bonus[$i];
		    			$bonus_count++;
		    		}
		    	}
		    }
		    if ($clean_bonus){
		    	$bonus = $clean_bonus;
		    }
		    //var_dump($bonus);
		    $sql = 'SELECT * FROM french WHERE size > ' . MIN_SIZE . ' AND size < ' . MAX_SIZE; // . ' ORDER BY size DESC';
		    $table = $bdd->query($sql);
		    if ($table === false){
		        echo $bdd->lastErrorMsg();
		    }

		    // error string
		    $tableaux =  array();
		    $interm_tab = strtolower($_POST['line1']);
		    for ($i=0; $i < 16; $i++) {
		        if ($interm_tab[$i] < 'a' || $interm_tab[$i] > 'z'){
		            die('Error string');
		        }

		    }
		        //result tab
		         echo  "<table class=\"board\"> <tr>";
		        for ($i=0; $i <= 15; $i++) {
		            echo "<td class=non-selected id=\"b$i\">". $interm_tab[$i] ."</td>";
		            if ($i == 3 || $i == 7 || $i == 11 ){
		                echo "</tr><tr>";
		            }
		        }
		        echo "</tr></table>";
		    $start = 0;
		    $end = MAX_WORD;

		    for ($count = 0; $count < MAX_WORD; $count++) {
		        $tableaux[$count] = substr($interm_tab, $start, $end);
		        $tableaux[$count] = str_split($tableaux[$count]);
		        $start += 4;
		    }

		    $count = 0;
		    $result = array();

		    function check_around($tableaux, $mot, $nbr, $save, $pos_x, $pos_y, $save_pos)
		    {
		        $nbr += 1;
		        $pos = array(
		            array(1, 0), array(0, 1), array(0, -1), array(-1, 0), array(1, 1), array(1, -1), array(-1, 1), array(-1, -1)
		        );

		        if ($nbr == strlen($mot)) {
		            return $save_pos;
		        }

		        for ($count = 0; $count < 8; $count++) {
		            $interm_y = $pos_y + $pos[$count][1];
		            $interm_x = $pos_x + $pos[$count][0];
		            if (
		                ($interm_x) >= 0 &&
		                ($interm_x) < MAX_WORD &&
		                ($interm_y) >= 0 &&
		                ($interm_y) < MAX_WORD
		            ) {
		                if ($tableaux
		                    [$interm_x]
		                    [$interm_y] == $mot[$nbr]
		                ) {
		                    if (empty($save[$interm_x][$interm_y]) || $save[$interm_x][$interm_y] === false) {
		                        $save[$interm_x][$interm_y] = true;
		                        $save_pos[$nbr][0] = $interm_x;
		                        $save_pos[$nbr][1] = $interm_y;
		                        $save_pos = check_around($tableaux, $mot, $nbr, $save, $interm_x, $interm_y, $save_pos);
		                        if ($save_pos) {
		                            return $save_pos;
		                        }
		                        else {
		                            $save[$interm_x][$interm_y] = false;
		                        }
		                    }
		                }
		            }
		        }
		        return false;
		    }

		    function algo($tableaux, $mot, $nbr, $save, $save_pos)
		    {
		        for ($count2 = 0; $count2 < MAX_WORD; $count2++) {
		            for ($count3 = 0; $count3 < MAX_WORD; $count3++) {
		                if ($tableaux[$count2][$count3] == $mot[$nbr]) {

		                    $save[$count2][$count3] = true;
		                    $save_pos = check_around($tableaux, $mot, $nbr, $save, $count2, $count3, $save_pos);
		                    if ($save_pos) {
	 		                    $save_pos[0][0] = $count2;
		                   		$save_pos[0][1] = $count3;
		                   		ksort($save_pos);
		                        return $save_pos;
		                    }
		                }
		            }
		        }
		        return false;
		    }
		    $tab = array(
		        "a" => 1,
		        'b' => 1,
		        "c" => 3,
		        "d" => 2,
		        "e" => 1,
		        "f" => 4,
		        "g" => 2,
		        "h" => 1,
		        "i" => 1,
		        "j" => 1,
		        "k" => 1,
		        "l" => 2,
		        "m" => 2,
		        "n" => 1,
		        "o" => 1,
		        "p" => 3,
		        "q" => 8,
		        "r" => 1,
		        "s" => 1,
		        "t" => 1,
		        "u" => 1,
		        "v" => 5,
		        "x" => 1,
		        "y" => 1,
		        "z" => 1,
		        );

		    function value_point($string, $tab, $save_pos, $bonus){
		        $char_tab = str_split($string);
		        $count = 0;
		        $result = 0;

		        foreach ($save_pos as $count2 => $value) {
		        	$check = $save_pos[$count2][0] * 4 + $save_pos[$count2][1];
		        	$save_pos[$count2][2] = 0;
		        	if ($bonus[$check]){
			        	$save_pos[$count2][2] = $bonus[$check];
					}
		        }

		        foreach ($char_tab as $key => $letter) {
		            foreach ($tab as $base => $nbr) {
		            	if ($save_pos[$count]){
			            		if ($save_pos[$count][2] == 1 && $base == $letter){
			            			$result += ($nbr * 2);
			            		}
				            	else if ($save_pos[$count][2] == 2 && $base == $letter){
				            		$result = $result +($nbr * 3);
				            	}
				                else if ($base == $letter){
				                    $result += $nbr;
				                }
				        }
			        }	
		            $count++;
		        }
		        foreach($save_pos as $count1 => $test){
		        	if ($save_pos[$count1][2] == 3){
		        		$result *= 2;
		        	}
		        	if ($save_pos[$count1][2] == 4){
		        		$result *= 3;
		        	}
		        }
	        return $result;
		    }
		    $save = array();
		    $save_pos = array();
		    $span_idx = 1;

		    //clean des accents
		    function accent_cleaner($str){
		    	$charset='utf-8';
			    $str = htmlentities($str, ENT_NOQUOTES, $charset);
			    
			    $str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
			    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
			    $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
		    	return ($str);
		    }

		    // ouverture de la base et envoie de donnée a l'algo et check des doublons.
		    while ($data = $table->fetchArray()) {
		        $mot = $data['word'];
		        $mot = accent_cleaner($mot);
	            $save_pos = algo($tableaux, $mot, 0, $save, $save_pos);
		        if ($save_pos) {
		           if ($count > 100) {
		               break;
		           }
		           if ($interm_check = end($result)){
		           		if (strcmp($interm_check['word'], $mot) != 0){
		           			reset($result);
		            		$result[] = array('word' =>$mot, 'score' => value_point($data['word'], $tab, $save_pos, $bonus), 'utf8' =>$data['word']);
		           		}
		           }
		         	else if (strcmp($result['word'], $mot) != 0) {
		            	$result[] = array('word' =>$mot, 'score' => value_point($data['word'], $tab, $save_pos, $bonus), 'utf8' =>$data['word']);
		        	 }
		            $count++;
		        }
		        $count2++;
		        $span_idx++;
		    }
		    //Triage par mots similaire

		    function similar($result){
		    	foreach ($result as $key => $value) {
		    		if ($result[$key + 1]){
			    		$length_current = strlen($result[$key]['word']);
						$current = $result[$key]['word'];
						$check = 0;
						$count = 1;
						while ($check < 1){
							if ($result[$key + $count]){
								$error = 0;
				    			$next_pointer = $result[$key + $count]['word'];
								$length_next = strlen($next_pointer);
								if (($length_current - $length_next) > -2 && ($length_current - $length_next) < 2){
									for ($i = 0; $i < strlen($current); $i++){
										if ($current[$i] != $next_pointer[$i]){
											$error++;
										}
										if ($error >= 2){
											break;
										}
									}
								}
								else {
									$error = 2;
								}
								if($error < 2){
									$result[$key]['similar'][] = $result[$key + $count]['utf8'];
									echo "<br>";
									unset($result[$key + $count]);
									sort($result);
								}
								$count++;
							}
							else{
								$check++;
							}
						}
					}
				}
		    	return $result;
		    }

		    $result = similar($result);
		    
		    //recuperation des points et tri en fonction des tw, dw, tl, dl
	       	usort($result, function ($a, $b){
	            return $a['score'] < $b['score'];
	        });
	        echo 'test';
	        var_dump($result);
		    $prev = "";
		    $truc = 0;
		    echo '<div class="result" id="result">';
		    echo '</div>';
		}
		else {
		    die('ERROR LINE EMPTY');
		}
		?>
		<script>
		var result = <?php echo json_encode($result); ?>;
		var recup_tab = <?php echo json_encode($tableaux); ?>;
		</script>
		<script src="js_file.js"></script>
	</body>
</html>