<?php

$day = 29;
$month = 2;
$year = 1998;

if(($day == 31 AND $month%2 == 0) OR ($day == 30 AND $month == 2)){
    $erreur = "Il n'y a pas 30 ou 31 jours dans ce mois";
}else{
    $strBirthdate = $year."-".$month."-".$day;

    if(isset($day) AND isset($month) AND isset($year)) {
        $birthdate = date("Y-n-j", strtotime($strBirthdate));
    }
    // $birthdate= htmlspecialchars($_POST['birthdate']);
}

echo $erreur;
if(isset($birthdate)){
    echo 1 . " ";
    echo $birthdate;
}
