 <?php
//Start
  $repInclude = './include/';
  require($repInclude . "_init.inc.php");

  // page inaccessible si visiteur non connecté
  if ( ! estVisiteurConnecte() )
  {
      header("Location: cSeConnecter.php");
  }else{
    $idUser = obtenirIdUserConnecte() ;
    $lgUser = obtenirDetailVisiteur($idConnexion, $idUser);
    $admin = $lgUser['admin'];
    //si non comptable,retour à l'accueil
      if( $admin == 0){
        header("Location:cAccueil.php");
      }
  }
  require($repInclude . "_entete.inc.php");
  
  //acquisition des données
  $tabQteEltsForfait=lireDonneePost("txtEltsForfait", "");
  $libelleHF = lireDonneePost("libelleHF", "");
  $moisSaisi=lireDonnee("lstMois", "");
  $utilSaisi=lireDonnee("lstUtil", "");
  $etape=lireDonnee("etape","");
  $etapeSaisie=lireDonneePost("etapeSaisie","");
  $etapeGet =lireDonnee("etapeGet","");
  $idLigneHF = lireDonnee("idLigneHF", "");
  
  if ($etape != "demanderConsult" && $etape != "validerConsult" && $etapeSaisie != "Corriger Saisie" && $etapeSaisie != "Valider Fiche" && $etapeGet != "validerSuppressionLigneHF") {
      // si autre valeur, on considère que c'est le début du traitement
      $etape = "demanderConsult";
  }
  
    if ($etapeGet == "validerSuppressionLigneHF") {
        if(substr($libelleHF,0,7) == 'REFUSE:') {
            ajouterErreur($tabErreurs,"Ce frais est déjà refusé");
        }else{
            refuserLigneHF($idConnexion, $idLigneHF);
        }

    }
  

  
if ($etape == "validerConsult") { // l'utilisateur valide ses nouvelles données
      // vérification de l'existence de la fiche de frais pour le mois demandé
    $existeFicheFrais = existeFicheFrais($idConnexion, $moisSaisi, $utilSaisi);
      // si elle n'existe pas, on la crée avec les élets frais forfaitisés à 0
    if ( !$existeFicheFrais ) {
        ajouterErreur($tabErreurs, "Il n'y a aucune fiche de frais pour ce utilisateur");
    }
}

if ($etapeSaisie == "Corriger Saisie" || $etapeSaisie =="Valider Fiche") {

    // l'utilisateur valide les éléments forfaitisés
    // vérification des quantités des éléments forfaitisés
    $ok = verifierEntiersPositifs($tabQteEltsForfait);
    if (!$ok) {
        ajouterErreur($tabErreurs, "Chaque quantité doit être renseignée et numérique positive.");        
    }
else {
        if($etapeSaisie == "Corriger Saisie"){
            // mise à jour des quantités des éléments forfaitisés
            modifierEltsForfait($idConnexion, $moisSaisi, $utilSaisi ,$tabQteEltsForfait);
        }
        if($etapeSaisie == "Valider Fiche"){
            //mise à jour si modification
            modifierEltsForfait($idConnexion, $moisSaisi, $utilSaisi ,$tabQteEltsForfait);
            //validation
            modifierEtatFicheFrais($idConnexion, $moisSaisi, $utilSaisi, 'VA');
        }
    }
        
        
        
}

        //récupération des données sur la fiche de frais demandée
        $tabFicheFrais = obtenirDetailFicheFrais($idConnexion, $moisSaisi, $utilSaisi);
          
// boolean selon Fiche
if($tabFicheFrais['idEtat'] == "CR" || $tabFicheFrais['idEtat'] == "CL" ){          
    $modif = 'true';
}else{
    $modif = 'false';
}
?>

<div id="contenu">
    <h2>Fiches de frais</h2>
    <form action="" method="post">
    <div class="corpsForm">
        <input type="hidden" name="etape" value="validerConsult" />
    <p style = "text-align:center">
      <select id="lstUtil" name="lstUtil" title="Sélectionnez le nom souhaité pour la fiche de frais">
          <?php
          //obtenir la liste des visiteurs
              $req = obtenirReqUsers();
              $idJeuUser = mysqli_query($idConnexion,$req);
              $lgUser = mysqli_fetch_assoc($idJeuUser);

              while ( is_array($lgUser) ) {
                $id = $lgUser["id"];
                $nom = $lgUser["nom"];
                $prenom = $lgUser["prenom"];
                $util = $lgUser["id"];
          ?>
          <option value="<?php echo $id ?>"<?php if ($utilSaisi == $id) { ?> selected="selected"<?php } ?>><?php echo  $nom?> <?php echo $prenom ?></option>

          <?php
                  $lgUser = mysqli_fetch_assoc($idJeuUser);
              }
              mysqli_free_result($idJeuUser);
          ?>

      </select>
      <select id="lstMois" name="lstMois" title="Sélectionnez le mois souhaité pour la fiche de frais">
          <?php
            // on propose tous les mois possibles
            $req = obtenirReqMoisFicheFraisDirect();
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
      <input class = "btn btn-primary" id="ok" type="submit" value="Valider" size="20"
             title="Demandez à consulter cette fiche de frais" />
    </p>
    </div>

    </form>
    <?php

    // demande et affichage des différents éléments (forfaitisés et non forfaitisés)
    // de la fiche de frais demandée, uniquement si pas d'erreur détecté au contrôle
        if ($etape != "demanderConsult" ) {
            if ( nbErreurs($tabErreurs) > 0 || $etape == "validerConsult") {
                echo toStringErreurs($tabErreurs) ;
            }
            else if($modif=='false'){
                echo '<h2>Plus de modification possible</h2>';
            }

            else{

                echo '<p class="info">Les modifications de la fiche de frais ont bien été enregistrées</p>';
            }
     ?>   



          <table class="listeLegere">
                   <tr>
                      <th class="date">Date</th>
                      <th class="libelle">Libellé</th>
                      <th class="montant">Montant</th>
                      <?php if($modif == 'true'){ ?>
                      <th class="action">&nbsp;</th> <?php } ?>
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
                            <form method="post" action="">
                            <input type="hidden" name="etapeGet" value="validerSuppressionLigneHF">
                            <input type="hidden" name="idLigneHF" value="<?= $lgEltHorsForfait["id"];?>">                                
                            <input type="hidden" name="lstUtil" value="<?= $utilSaisi; ?>">
                            <input type="hidden" name="lstMois" value="<?= $moisSaisi; ?>">
                                                            <input type="hidden" name="libelleHF" value="<?= $lgEltHorsForfait["libelle"];?>">
                      <tr>
                         <td><?php echo $lgEltHorsForfait["date"] ; ?></td>
                         <td><?php echo filtrerChainePourNavig($lgEltHorsForfait["libelle"]) ; ?></td>
                         <td><?php echo $lgEltHorsForfait["montant"] ; ?></td>
                         <?php if($modif == 'true'){ ?>
                         <td><input class = "btn btn-primary" style="width:100%;"
                                    type="submit" onclick="return confirm('Voulez-vous vraiment refuser cette ligne de frais hors forfait ?');" 
                                    title="Refuser la ligne de frais hors forfait" value="Refuser"></td> <?php } ?>
                      </tr>
                            </form>
                  <?php
                      $lgEltHorsForfait = mysqli_fetch_assoc($idJeuEltsHorsForfait);
                  }
                  mysqli_free_result($idJeuEltsHorsForfait);
        ?>
          </table>
        </form>
            <form action="" method="post">
            <div class="corpsForm">
                
                <input type="hidden" name="lstMois" value="<?php echo $moisSaisi ; ?>" />
                <input type="hidden" name="lstUtil" value="<?php echo $utilSaisi ;?>" />
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
                      <?php if($modif == 'false'){ ?>
                          disabled="disabled"
                      <?php } ?>
                />

              <?php
                  $lgEltForfait = mysqli_fetch_assoc($idJeuEltsFraisForfait);
              }
              mysqli_free_result($idJeuEltsFraisForfait);
              ?>
            </fieldset>
                <?php if ($modif=='true'){ ?>
            <div class="piedForm">
              <input class = "btn btn-primary"  type="submit" name="etapeSaisie" value="Corriger Saisie" size="20"
                     title="Enregistrer les nouvelles valeurs des éléments forfaitisés" />  
              <input class = "btn btn-primary"  type="submit" name="etapeSaisie" value="Valider Fiche" size="20"
                     title="valider la fiche de frais du visiteur pour ce mois" onclick="return confirm('Voulez-vous vraiment valider cette fiche de frais ? Vous ne pourrez plus la modifier ensuite');" />
            </div>
                <?php } ?>
            </form>
        </div>

    <?php
      }
    

    ?>
      </div>
</div>
</div>






<?php
  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");
?>
