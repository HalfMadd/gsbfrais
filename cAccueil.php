<?php
/**
 * Page d'accueil de l'application web AppliFrais

 * @package default

 * @todo  RAS

 */


$repInclude = './include/';

require($repInclude . "_init.inc.php");


// page inaccessible si visiteur non connecté

if (!estVisiteurConnecte()) {

    header("Location: cSeConnecter.php");
}

require($repInclude . "_entete.inc.php");

$etape = lireDonneePost("etape", "");

if ($etape == "sauvegarde") {

    backup();
} elseif ($etape == "restaure") {

    restaure();
}
?>

<!-- Division principale -->

<div id="contenu">

    <h2>Bienvenue sur l'intranet GSB</h2>

    <!-- <form action="" method="post">

      <input type="hidden" name="etape" value="sauvegarde" />

      <input  class = "btn btn-primary" id="ok" type="submit" value="Sauvegarder BD" size="20"/>

    </form>

    <form action="" method="post">

      <input type="hidden" name="etape" value="restaure" />

      <input  class = "btn btn-primary" id="ok" type="submit" value="Restaurer BD" size="20" />

    </form> -->

</div>

<?php
require($repInclude . "_pied.inc.html");

require($repInclude . "_fin.inc.php");
?>

