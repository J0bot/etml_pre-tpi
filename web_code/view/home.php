<?php 
//Auteur : José Carlos Gasser
//Date : 14.03.2022
//Description : Contenu de la page home
?>
<?= $state?>

<form action="?state=0" method="post">
    <input type="submit" value="Eteindre">
</form>
<form action="?state=1" method="post">
    <input type="submit" value="Allumer">
</form>


