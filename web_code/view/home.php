<?php 
//Auteur : José Carlos Gasser
//Date : 14.03.2022
//Description : Contenu de la page home
?>
<?= $state?>

<?= $delay?>


<form action="?state=0" method="GET">
    <input name="state" type="textbox" value="0">

    <input type="submit" value="Eteindre">
    
</form>
<form action="?state=1" method="GET">
    <input name="state" type="textbox" value="1">
    <input type="submit" value="Allumer">
</form>



<form action="?delay" method="GET">
    <input name="delay" type="textbox" value="1000">
    <input type="submit" value="Delay">     

</form>


