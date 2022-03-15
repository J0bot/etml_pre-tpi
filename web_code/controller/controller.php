<?php 
 //Auteur : José Carlos Gasser
 //Date : 01.11.2021
 //Description : controller de l'application

//Il faut inclure le model dans le controller
include("model/model.php");

//On inclus le header tout en haut de la page
include('view/header.php');

$conn = new Database;

$state = 0;
$delay = 0;
if(isset($_POST))
{
    $state = $conn->changeState(1);
}

echo $_POST;

$state = $conn->getState();
//$delay = $conn->getDelay();

include("view/home.php");

include("view/footer.php");

?>
