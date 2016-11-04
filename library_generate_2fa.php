<?php
function random2fa() {
    $alphabet = "0123456789";
    $fa = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 6; $i++) {
        $n = rand(0, $alphaLength);
        $fa[] = $alphabet[$n];
    }
    return implode($fa); //turn the array into a string
}
?>