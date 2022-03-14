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

if(isset($_GET["state"]))
{
    $state = $conn->changeState($_GET["state"]);
}

$state = $conn->getState();

include("view/home.php");

include("view/footer.php");

?>
