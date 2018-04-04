 <?php
/**
 * Page d'accueil de l'application web AppliFrais
 * @package default
 * @todo  RAS
 */
  $repInclude = './include/';
  require($repInclude . "_init.inc.php");

  // page inaccessible si visiteur non connecté
  if ( ! estVisiteurConnecte() )
  {
        header("Location: cSeConnecter.php");
  }else{
    $idUser = obtenirIdUserConnecte() ;
    $infosUser = obtenirDetailVisiteur($idConnexion, $idUser);
    $admin = $infosUser['admin'];
      if( $admin == 0){
        header("Location:cAccueil.php");
      }
  }
  require($repInclude . "_entete.inc.php");
  
  $tabQteEltsForfait=lireDonneePost("txtEltsForfait", "");
  $moisSaisi= lireDonnee("lstMois","");
  $utilSaisi=lireDonneePost("lstUtil", "");
  $etape=lireDonneePost("etape","");
  $etapeSaisie=lireDonneePost("etapeSaisie","");
  $etapeGet =lireDonnee("etapeGet","");
  $idLigneHF = lireDonnee("idLigneHF", "");
  
if ($etape != "demanderSuivi" && $etape != "validerSuivi" && $etape != "Mettre en paiement" ) {
      // si autre valeur, on considère que c'est le début du traitement
      $etape = "demanderSuivi";
}
  
if ($etape == "validerSuivi") { // l'utilisateur valide ses nouvelles données
      // vérification de l'existence de la fiche de frais pour le mois demandé
    $existeFicheFrais = existeFicheFrais($idConnexion, $moisSaisi, $utilSaisi);
      // si elle n'existe pas, erreur
    if ( !$existeFicheFrais ) {
        ajouterErreur($tabErreurs, "Il n'y a aucune fiche de frais pour cet utilisateur");
    }
}

if ($etape =="Mettre en paiement") {
    $req=modifierEtatFicheFrais($idConnexion, $moisSaisi, $utilSaisi, 'RB');
}

        //récupération des données sur la fiche de frais demandée
        $tabFicheFrais = obtenirDetailFicheFrais($idConnexion, $moisSaisi, $utilSaisi);
        if($tabFicheFrais['idEtat'] == 'RB'){
             ajouterErreur($tabErreurs,"Cette fiche est déjà mise en paiement");
        }
          
          
?>
<div id="contenu">
  <h2>Suivre le paiement fiche de frais</h2>
        <?php
              //tous les utilisateurs qui ont une fiche a mettre en remboursement
            $req = obtenirReqUsersSuivi();
            $idJeuUser = mysqli_query($idConnexion,$req);
            if(mysqli_num_rows($idJeuUser) != 0){
            ?>
  <form action="" method="post">
  <div class="corpsForm">
      <input type="hidden" name="etape" value="validerSuivi" />
  <p style = "text-align:center">

    <select id="lstUtil" name="lstUtil" title="Sélectionnez le nom souhaité pour la fiche de frais">
        <?php


            $lgUser = mysqli_fetch_assoc($idJeuUser);

            while ( is_array($lgUser) ) {
              $id = $lgUser["id"];
              $nom = $lgUser["nom"];
              $prenom = $lgUser["prenom"];
              $util = $lgUser["id"];
        ?>
        <option value="<?php echo $id ?>"<?php if ($utilSaisi == $id) { ?> selected="selected"<?php } ?> > <?php echo  $nom?> <?php echo $prenom ?></option>

        <?php
                $lgUser = mysqli_fetch_assoc($idJeuUser);
            }
            mysqli_free_result($idJeuUser);
        ?>

    </select>
       <select id="lstMois" name="lstMois" title="Sélectionnez le mois souhaité pour la fiche de frais">
          <?php
            // on propose tous les mois possibles
            $req = obtenirReqMoisFicheFraisSuivi();
            $idJeuMois = mysqli_query($idConnexion,$req);
            $lgMois = mysqli_fetch_assoc($idJeuMois);
            while ( is_array($lgMois) ) {
                $mois = $lgMois["mois"];
                $noMois = intval(substr($mois, 4, 2));
                $annee = intval(substr($mois, 0, 4));
          ?>
          <option value="<?php echo $mois; ?>"<?php if ($moisSaisi == $mois) { ?> selected="selected"<?php } ?>><?php echo obtenirLibelleMois($noMois) . " " . $annee; ?></option>
          <?php
                  $lgMois = mysqli_fetch_assoc($idJeuMois);
              }
              mysqli_free_result($idJeuMois);
          ?>
      </select>

  </p>
  </div>
  <div class="piedForm">
  <p>
    <input  class = "btn btn-primary" id="ok" type="submit" value="Valider" size="20"
           title="Demandez à consulter cette fiche de frais" />
  </p>
  </div>

  </form>
        <?php }else{ ?>
        <h2>Aucune fiche à mettre en remboursement</h2>
        <?php } ?>
  <?php

  // demande et affichage des différents éléments (forfaitisés et non forfaitisés)
  // de la fiche de frais demandée, uniquement si pas d'erreur détecté au contrôle
    if($etape=="Mettre en paiement"){
        echo '<p class="info">La fiche de frais est bien placée en remboursement</p>';
    }
    
    if ($etape == "validerSuivi" ) {
        if($tabFicheFrais['idEtat']!='VA'){
            ajouterErreur($tabErreurs, 'Cette fiche de frais n\'est pas validée' );
        }
        if ( nbErreurs($tabErreurs) > 0 ) {
            echo toStringErreurs($tabErreurs);
        }
          else {
    ?>
    <h3>Fiche de frais du mois de <?php echo obtenirLibelleMois(intval(substr($moisSaisi,4,2))) . " " . substr($moisSaisi,0,4); ?> :
    <em><?php echo $tabFicheFrais["libelleEtat"]; ?> </em>
    depuis le <em><?php echo $tabFicheFrais["dateModif"]; ?></em></h3>
    <div class="encadre">

      <table class="listeLegere">
               <tr>
                  <th class="date">Date</th>
                  <th class="libelle">Libellé</th>
                  <th class="montant">Montant</th>

               </tr>
  <?php
              // demande de la requête pour obtenir la liste des éléments hors
              // forfait du visiteur connecté pour le mois demandé
              $req = obtenirReqEltsHorsForfaitFicheFrais($moisSaisi, $utilSaisi);
              $idJeuEltsHorsForfait = mysqli_query( $idConnexion,$req);
              $lgEltHorsForfait = mysqli_fetch_assoc($idJeuEltsHorsForfait);

              // parcours des éléments hors forfait
              while ( is_array($lgEltHorsForfait) ) {
              ?>
                  <tr>
                     <td><?php echo $lgEltHorsForfait["date"] ; ?></td>
                     <td><?php echo filtrerChainePourNavig($lgEltHorsForfait["libelle"]) ; ?></td>
                     <td><?php echo $lgEltHorsForfait["montant"] ; ?></td>
                  </tr>
              <?php
                  $lgEltHorsForfait = mysqli_fetch_assoc($idJeuEltsHorsForfait);
              }
              mysqli_free_result($idJeuEltsHorsForfait);
    ?>
      </table>
      <form action="" method="post">
      <div class="corpsForm">
        <fieldset>
          <legend>Eléments forfaitisés
          </legend>
    <?php
          // demande de la requête pour obtenir la liste des éléments
          // forfaitisés du visiteur connecté pour le mois demandé
          $req = obtenirReqEltsForfaitFicheFrais($moisSaisi, $utilSaisi);
          $idJeuEltsFraisForfait = mysqli_query($idConnexion, $req);
          echo mysqli_error($idConnexion);
          $lgEltForfait = mysqli_fetch_assoc($idJeuEltsFraisForfait);
          while ( is_array($lgEltForfait) ) {
              $idFraisForfait = $lgEltForfait["idFraisForfait"];
              $libelle = $lgEltForfait["libelle"];
              $quantite = $lgEltForfait["quantite"];
          ?>

            <label for="<?php echo $idFraisForfait ?>"><?php echo $libelle; ?></label>
            <input type="text" id="<?php echo $idFraisForfait ?>"
                  name="txtEltsForfait[<?php echo $idFraisForfait ?>]"
                  size="10" maxlength="5"
                  title="Entrez la quantité de l'élément forfaitisé"
                  value="<?php echo $quantite; ?>" 
                  disabled = "disabled"/>

          <?php
              $lgEltForfait = mysqli_fetch_assoc($idJeuEltsFraisForfait);
          }
          mysqli_free_result($idJeuEltsFraisForfait);
          ?>
        </fieldset>
        <div class="piedForm">
                <input type="hidden" name="lstMois" value="<?php echo $moisSaisi ; ?>" />
                <input type="hidden" name="lstUtil" value="<?php echo $utilSaisi ;?>" />
          <input class = "btn btn-primary"  id="ok" type="submit" name="etape" value="Mettre en paiement" size="20"
                 title="Enregistrer les nouvelles valeurs des éléments forfaitisés" />
        </div>
        </form>
    </div>

  </div>
          <?php
            }
          }

          ?>
</div>
<?php
  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");
?>
