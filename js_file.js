
function check_around(mots, grille, pos_y, pos_x, depth, save_pos, checked){
    var interm_x = 0;
    var interm_y = 0;
    if (mots.length == depth){
        return true;
    }

    var positions = [
        [1, 0], 
        [0, 1],
        [0, -1],
        [-1, 0],
        [1, 1],
        [1, -1],
        [-1, 1],
        [-1, -1]
    ];
    for (var current_pos = 0; current_pos < 8; current_pos++){
        interm_y = pos_y + positions[current_pos][0];
        interm_x = pos_x + positions[current_pos][1];

        // Si la position à checker sort de la grille, on passe à la suivante
        if (interm_y < 0  || interm_y > 3 || interm_x < 0 || interm_x > 3){
            continue;
        }

        // La lettre correspond pas ? on passe à la position suivante
        if (grille[interm_y][interm_x] !== mots[depth]){
            continue;
        }
        // la case a déjà été utilisée ? On passe à la suivante

        if (checked[interm_y][interm_x] !== false){
            continue;
        }

        // Donc, ici : j'ai :
        // - une position dans la grille
        // - une lettre qui correspond
        // - une lettre pas encore utilisée


        // On note que cette case a été checkée
        checked[interm_y][interm_x] = true;

        // On enregistre les coords de la lettre trouvée
        save_pos[depth] = [interm_y, interm_x];

        // Et on relance la machine pour la lettre suivante
        var found = check_around(mots, grille, interm_y, interm_x, depth+1, save_pos, checked);
        if (found){
            return true;
        }
        else{
            save_pos[depth] = false;
            checked[interm_y][interm_x] = false;
        }
    }
    return false;
}

function reset_checked(checked){
    checked = [];
    for (var ligne = 0; ligne < 4; ligne++){
        checked[ligne] = [];
        for (var colonne = 0; colonne < 4; colonne++){
            checked[ligne][colonne] = false;
        }
    }
    return checked;
}

function start_algo(mot_courant, grille){
    var save_pos = [];
    var checked = reset_checked(checked);
    // On boucle sur les lignes
    for (var ligne = 0; ligne < 4; ligne++){
        // On boucle sur les cases de la ligne
        for (var colonne = 0; colonne < 4; colonne++){
            // la case ne correspond PAS à la 1ere lettre du mot, on passe !
            if (mot_courant[0] != grille[ligne][colonne]){
                continue;
            }
            // on note 
            save_pos[0] = [ligne, colonne];
            checked[ligne] = checked[ligne] || [];
            checked[ligne][colonne] = true;
            var if_true = check_around(mot_courant, grille, ligne, colonne, 1, save_pos, checked);
            if (if_true === true){
                //console.log(save_pos);
                return save_pos;
            }
        }
    }
    return false;
}
function aff_square(color, td, current_square, timer){
    td.className = "selected";
    td.style.backgroundColor = color;
    td.style.transitionDuration = "1s";

}


var count1 = 0;
var count = 0;
var mots = 0;
var save_pos = [];
for (var key in result){
    mots = result[key]['word'];
    save_pos[key] = start_algo(mots, recup_tab);
    }
var new_key = 0;
var paragraph = document.createElement('p');  
document.addEventListener('keypress', function(e){
    if (e.charCode == 32){
            var r = 255
            var g = 0;
            var b = 0;
            e.preventDefault()
            console.log(result[new_key]['similar']); 
            paragraph.innerHTML = result[new_key]['utf8'];
            if (result[new_key]['similar']){
                paragraph.innerHTML += '<br>';
                paragraph.innerHTML += result[new_key]['similar'];
            }
            var div = document.getElementById('result');
            div.appendChild(paragraph);
            for (var i = 0; i < 16; i++){
                var reset = document.getElementById('b' + i);
                reset.className = "non-selected";
                reset.style.background = "rgb(255,255,255)";
                reset.style.transitionDuration = "0s";
            }
            for (var count = 0; count < save_pos[new_key].length; count++){
                (function(save_pos, new_key, count, timer){
                    var timer = setTimeout(function() {
                    if (g < 236){
                       g += 20;
                    }
                    if (b < 245){
                       b += 10;
                    }
                    var color = "rgb("+r+","+g+","+b+")";
                    var current_square = save_pos[new_key][count][0] * 4 + save_pos[new_key][count][1];
                    var td = document.getElementById('b' + current_square);
                    //console.log("td1 = ", td);
                    aff_square(color, td, current_square, timer);
                    }, timer);

                })(save_pos, new_key, count, 200 * (count + 1));
            }
        }
    new_key++;
}, false);
