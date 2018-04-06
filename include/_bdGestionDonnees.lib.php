<?php 

/**

 * Regroupe les fonctions d'acc�s aux donn�es.

 * @package default

 * @author Arthur Martin

 * @todo Fonctions retournant plusieurs lignes sont � r��crire.

 */



/**

 * Se connecte au serveur de donn�es mysqli.

 * Se connecte au serveur de donn�es mysqli � partir de valeurs

 * pr�d�finies de connexion (h�te, compte utilisateur et mot de passe).

 * Retourne l'identifiant de connexion si succ�s obtenu, le bool�en false

 * si probl�me de connexion.

 * @return resource identifiant de connexion

 */

function connecterServeurBD() {

    $hote = "localhost";

    $login = "id4909693_root";

    $mdp = "gsb_mdp";

    return mysqli_connect($hote, $login, $mdp);

}



/**

 * S�lectionne (rend active) la base de donn�es.

 * S�lectionne (rend active) la BD pr�d�finie gsb_frais sur la connexion

 * identifi�e par $idCnx. Retourne true si succ�s, false sinon.

 * @param resource $idCnx identifiant de connexion

 * @return boolean succ�s ou �chec de s�lection BD

 */ 

function activerBD($idCnx) {

    $bd = "id4909693_gsb_valide";

    $query = "SET CHARACTER SET utf8";

    // Modification du jeu de caract�res de la connexion

    $res = mysqli_query($idCnx ,$query);

    $ok = mysqli_select_db($idCnx, $bd);

    return $ok;

}



/**

 * Ferme la connexion au serveur de donn�es.

 * Ferme la connexion au serveur de donn�es identifi�e par l'identifiant de

 * connexion $idCnx.

 * @param resource $idCnx identifiant de connexion

 * @return void

 */

function deconnecterServeurBD($idCnx) {

    mysqli_close($idCnx);

}



/**

 * Echappe les caract�res sp�ciaux d'une cha�ne.

 * Envoie la cha�ne $str �chapp�e, c�d avec les caract�res consid�r�s sp�ciaux

 * par mysqli (tq la quote simple) pr�c�d�s d'un \, ce qui annule leur effet sp�cial

 * @param string $str cha�ne � �chapper

 * @return string cha�ne �chapp�e

 */

function filtrerChainePourBD($str) {

    if ( ! get_magic_quotes_gpc() ) {

        // si la directive de configuration magic_quotes_gpc est activ�e dans php.ini,

        // toute cha�ne re�ue par get, post ou cookie est d�j� �chapp�e

        // par cons�quent, il ne faut pas �chapper la cha�ne une seconde fois

        $str = mysqli_real_escape_string(connecterServeurBD(), $str);

    }

    return $str;

}



/**

 * Fournit les informations sur un visiteur demand�.

 * Retourne les informations du visiteur d'id $unId sous la forme d'un tableau

 * associatif dont les cl�s sont les noms des colonnes(id, nom, prenom).

 * @param resource $idCnx identifiant de connexion

 * @param string $unId id de l'utilisateur

 * @return array  tableau associatif du visiteur

 */

function obtenirDetailVisiteur($idCnx, $unId) {

    $id = filtrerChainePourBD($unId);

    $requete = "select id, nom, prenom, admin from visiteur where id='" . $unId . "'";

    $idJeuRes = mysqli_query($idCnx, $requete);

    $ligne = false;

    if ( $idJeuRes ) {

        $ligne = mysqli_fetch_assoc($idJeuRes);

        mysqli_free_result($idJeuRes);

    }

    return $ligne ;

}



/**

 * Fournit les informations d'une fiche de frais.

 * Retourne les informations de la fiche de frais du mois de $unMois (MMAAAA)

 * sous la forme d'un tableau associatif dont les cl�s sont les noms des colonnes

 * (nbJustitificatifs, idEtat, libelleEtat, dateModif, montantValide).

 * @param resource $idCnx identifiant de connexion

 * @param string $unMois mois demand� (MMAAAA)

 * @param string $unIdVisiteur id visiteur

 * @return array tableau associatif de la fiche de frais

 */

function obtenirDetailFicheFrais($idCnx, $unMois, $unIdVisiteur) {

    $unMois = filtrerChainePourBD($unMois);

    $ligne = false;

    $requete="select IFNULL(nbJustificatifs,0) as nbJustificatifs, etat.id as idEtat, libelle as libelleEtat, dateModif, montantValide

    from fichefrais inner join etat on idEtat = etat.id

    where idVisiteur='" . $unIdVisiteur . "' and mois='" . $unMois . "'";

    $idJeuRes = mysqli_query($idCnx, $requete);

    if ( $idJeuRes ) {

        $ligne = mysqli_fetch_assoc($idJeuRes);

    }

    mysqli_free_result($idJeuRes);



    return $ligne ;

}



/**

 * V�rifie si une fiche de frais existe ou non.

 * Retourne true si la fiche de frais du mois de $unMois (MMAAAA) du visiteur

 * $idVisiteur existe, false sinon.

 * @param resource $idCnx identifiant de connexion

 * @param string $unMois mois demand� (MMAAAA)

 * @param string $unIdVisiteur id visiteur

 * @return bool�en existence ou non de la fiche de frais

 */

function existeFicheFrais($idCnx, $unMois, $unIdVisiteur) {

    $unMois = filtrerChainePourBD($unMois);

    $requete = "select idVisiteur from fichefrais where idVisiteur='" . $unIdVisiteur .

              "' and mois='" . $unMois . "'";

    $idJeuRes = mysqli_query($idCnx, $requete);

    $ligne = false ;

    if ( $idJeuRes ) {

        $ligne = mysqli_fetch_assoc($idJeuRes);

        mysqli_free_result($idJeuRes);

    }



    // si $ligne est un tableau, la fiche de frais existe, sinon elle n'exsite pas

    return is_array($ligne) ;

}



/**

 * Fournit le mois de la derni�re fiche de frais d'un visiteur.

 * Retourne le mois de la derni�re fiche de frais du visiteur d'id $unIdVisiteur.

 * @param resource $idCnx identifiant de connexion

 * @param string $unIdVisiteur id visiteur

 * @return string dernier mois sous la forme AAAAMM

 */

function obtenirDernierMoisSaisi($idCnx, $unIdVisiteur) {

	$requete = "select max(mois) as dernierMois from fichefrais where idVisiteur='" .

            $unIdVisiteur . "'";

	$idJeuRes = mysqli_query($idCnx, $requete);

    $dernierMois = false ;

    if ( $idJeuRes ) {

        $ligne = mysqli_fetch_assoc($idJeuRes);

        $dernierMois = $ligne["dernierMois"];

        mysqli_free_result($idJeuRes);

    }

	return $dernierMois;

}



/**

 * Ajoute une nouvelle fiche de frais et les �l�ments forfaitis�s associ�s,

 * Ajoute la fiche de frais du mois de $unMois (MMAAAA) du visiteur

 * $idVisiteur, avec les �l�ments forfaitis�s associ�s dont la quantit� initiale

 * est affect�e � 0. Cl�t �ventuellement la fiche de frais pr�c�dente du visiteur.

 * @param resource $idCnx identifiant de connexion

 * @param string $unMois mois demand� (MMAAAA)

 * @param string $unIdVisiteur id visiteur

 * @return void

 */

function ajouterFicheFrais($idCnx, $unMois, $unIdVisiteur) {

    $unMois = filtrerChainePourBD($unMois);

    //ne pas faire de fiche pour les comptables

    $check = obtenirDetailVisiteur($idCnx, $unIdVisiteur);

    if($check['admin']!= '1'){

    // modification de la derni�re fiche de frais du visiteur

    $dernierMois = obtenirDernierMoisSaisi($idCnx, $unIdVisiteur);

	$laDerniereFiche = obtenirDetailFicheFrais($idCnx, $dernierMois, $unIdVisiteur);

	if ( is_array($laDerniereFiche) && $laDerniereFiche['idEtat']=='CR'){

		modifierEtatFicheFrais($idCnx, $dernierMois, $unIdVisiteur, 'CL');

	}



    // ajout de la fiche de frais � l'�tat Cr��

    $requete = "insert into fichefrais (idVisiteur, mois, nbJustificatifs, montantValide, idEtat, dateModif) values ('"

              . $unIdVisiteur

              . "','" . $unMois . "',0,NULL, 'CR', '" . date("Y-m-d") . "')";

    mysqli_query($idCnx, $requete);



    // ajout des �l�ments forfaitis�s

    $requete = "select id from fraisforfait";

    $idJeuRes = mysqli_query($idCnx, $requete);

    if ( $idJeuRes ) {

        $ligne = mysqli_fetch_assoc($idJeuRes);

        while ( is_array($ligne) ) {

            $idFraisForfait = $ligne["id"];

            // insertion d'une ligne frais forfait dans la base

            $requete = "insert into lignefraisforfait (idVisiteur, mois, idFraisForfait, quantite)

                        values ('" . $unIdVisiteur . "','" . $unMois . "','" . $idFraisForfait . "',0)";

            mysqli_query($idCnx, $requete);

            // passage au frais forfait suivant

            $ligne = mysqli_fetch_assoc ($idJeuRes);

        }

        mysqli_free_result($idJeuRes);

    }

    }

}



/**

 * Retourne le texte de la requ�te select concernant les mois pour lesquels un

 * visiteur a une fiche de frais.

 *

 * La requ�te de s�lection fournie permettra d'obtenir les mois (AAAAMM) pour

 * lesquels le visiteur $unIdVisiteur a une fiche de frais.

 * @param string $unIdVisiteur id visiteur

 * @return string texte de la requ�te select

 */

function obtenirReqMoisFicheFrais($unIdVisiteur) {

    $req = "select fichefrais.mois as mois from  fichefrais where fichefrais.idvisiteur ='"

            . $unIdVisiteur . "' order by fichefrais.mois desc ";

    return $req ;

}



function obtenirReqMoisFicheFraisDirect() {

    $req = "select DISTINCT fichefrais.mois as mois from fichefrais where idEtat='CR' OR idEtat='CL' order by fichefrais.mois desc ";

    return $req ;

}



function obtenirReqMoisFicheFraisSuivi(){

    $req ="select DISTINCT fichefrais.mois as mois from fichefrais where idEtat='VA' order by fichefrais.mois desc";

    return $req;   

}





function obtenirReqUsers() {

    $req = "select  prenom, nom, id from visiteur where admin = 0;";

    return $req ;

}



function obtenirReqUsersSuivi() {

    $req = "select DISTINCT prenom, nom, id from visiteur JOIN fichefrais ON id = `idVisiteur` where admin = 0  and idEtat = 'VA'";

    return $req ;

    

}





/**

 * Retourne le texte de la requ�te select concernant les �l�ments forfaitis�s

 * d'un visiteur pour un mois donn�s.

 *

 * La requ�te de s�lection fournie permettra d'obtenir l'id, le libell� et la

 * quantit� des �l�ments forfaitis�s de la fiche de frais du visiteur

 * d'id $idVisiteur pour le mois $mois

 * @param string $unMois mois demand� (MMAAAA)

 * @param string $unIdVisiteur id visiteur

 * @return string texte de la requ�te select

 */

function obtenirReqEltsForfaitFicheFrais($unMois, $unIdVisiteur) {

    $unMois = filtrerChainePourBD($unMois);

    $requete = "select idFraisForfait, libelle, quantite from lignefraisforfait

              inner join fraisforfait on fraisforfait.id = lignefraisforfait.idFraisForfait

              where idVisiteur='" . $unIdVisiteur . "' and mois='" . $unMois . "'";

    return $requete;

}



/**

 * Retourne le texte de la requ�te select concernant les �l�ments hors forfait

 * d'un visiteur pour un mois donn�s.

 *

 * La requ�te de s�lection fournie permettra d'obtenir l'id, la date, le libell�

 * et le montant des �l�ments hors forfait de la fiche de frais du visiteur

 * d'id $idVisiteur pour le mois $mois

 * @param string $unMois mois demand� (MMAAAA)

 * @param string $unIdVisiteur id visiteur

 * @return string texte de la requ�te select

 */

function obtenirReqEltsHorsForfaitFicheFrais($unMois, $unIdVisiteur) {

    $unMois = filtrerChainePourBD($unMois);

    $requete = "select id, date, libelle, montant from lignefraishorsforfait

              where idVisiteur='" . $unIdVisiteur

              . "' and mois='" . $unMois . "'";

    return $requete;

}



/**

 * Supprime une ligne hors forfait.

 * Supprime dans la BD la ligne hors forfait d'id $unIdLigneHF

 * @param resource $idCnx identifiant de connexion

 * @param string $idLigneHF id de la ligne hors forfait

 * @return void

 */

function supprimerLigneHF($idCnx, $unIdLigneHF) {

    $requete = "delete from lignefraishorsforfait where id = " .$unIdLigneHF;

    mysqli_query($idCnx, $requete);

}



function refuserLigneHF($idCnx, $unIdLigneHF) {

    $requeteTake = "select libelle from lignefraishorsforfait where id =" . $unIdLigneHF;

    $refuse = mysqli_query($idCnx, $requeteTake);

    $libelle= mysqli_fetch_assoc($refuse);

    if (preg_match('/REFUSE: /',$libelle["libelle"])){

      $texte = $libelle["libelle"];

    }else{

      $texte = "REFUSE: " . $libelle["libelle"];

    }

    $requetePut = "update lignefraishorsforfait set libelle = '" . $texte

                . " ' where id = " . $unIdLigneHF;

    mysqli_query($idCnx, $requetePut);

    return $texte;

}

/**

 * Ajoute une nouvelle ligne hors forfait.

 * Ins�re dans la BD la ligne hors forfait de libell� $unLibelleHF du montant

 * $unMontantHF ayant eu lieu � la date $uneDateHF pour la fiche de frais du mois

 * $unMois du visiteur d'id $unIdVisiteur

 * @param resource $idCnx identifiant de connexion

 * @param string $unMois mois demand� (AAMMMM)

 * @param string $unIdVisiteur id du visiteur

 * @param string $uneDateHF date du frais hors forfait

 * @param string $unLibelleHF libell� du frais hors forfait

 * @param double $unMontantHF montant du frais hors forfait

 * @return void

 */

function ajouterLigneHF($idCnx, $unMois, $unIdVisiteur, $uneDateHF, $unLibelleHF, $unMontantHF) {

    $unLibelleHF = filtrerChainePourBD($unLibelleHF);

    $uneDateHF = filtrerChainePourBD(convertirDateFrancaisVersAnglais($uneDateHF));

    $unMois = filtrerChainePourBD($unMois);

    $requete = "insert into lignefraishorsforfait(idVisiteur, mois, date, libelle, montant)

                values ('" . $unIdVisiteur . "','" . $unMois . "','" . $uneDateHF . "','" . $unLibelleHF . "'," . $unMontantHF .")";

    mysqli_query($idCnx, $requete);

}



/**

 * Modifie les quantit�s des �l�ments forfaitis�s d'une fiche de frais.

 * Met � jour les �l�ments forfaitis�s contenus

 * dans $desEltsForfaits pour le visiteur $unIdVisiteur et

 * le mois $unMois dans la table LigneFraisForfait, apr�s avoir filtr�

 * (annul� l'effet de certains caract�res consid�r�s comme sp�ciaux par

 *  mysqli) chaque donn�e

 * @param resource $idCnx identifiant de connexion

 * @param string $unMois mois demand� (MMAAAA)

 * @param string $unIdVisiteur  id visiteur

 * @param array $desEltsForfait tableau des quantit�s des �l�ments hors forfait

 * avec pour cl�s les identifiants des frais forfaitis�s

 * @return void

 */

function modifierEltsForfait($idCnx, $unMois, $unIdVisiteur, $desEltsForfait) {

    $unMois=filtrerChainePourBD($unMois);

    $unIdVisiteur=filtrerChainePourBD($unIdVisiteur);

    foreach ($desEltsForfait as $idFraisForfait => $quantite) {

        $requete = "update lignefraisforfait set quantite = " . $quantite

                    . " where idVisiteur = '" . $unIdVisiteur . "' and mois = '"

                    . $unMois . "' and idFraisForfait='" . $idFraisForfait . "'";

      mysqli_query($idCnx, $requete);

    }

    $mois = date("Y-m-d");

      $requete = "update fichefrais set dateModif = '". $mois ."'

                 where idVisiteur = '" . $unIdVisiteur . "' and mois = '"

                . $unMois . "'";

      mysqli_query($idCnx, $requete);

    return $requete;

}

/**

 * Modifie l'�tat et la date de modification d'une fiche de frais



 * Met � jour l'�tat de la fiche de frais du visiteur $unIdVisiteur pour

 * le mois $unMois � la nouvelle valeur $unEtat et passe la date de modif �

 * la date d'aujourd'hui

 * @param resource $idCnx identifiant de connexion

 * @param string $unIdVisiteur

 * @param string $unMois mois sous la forme aaaamm

 * @return void

 */

function modifierEtatFicheFrais($idCnx, $unMois, $unIdVisiteur, $unEtat) {

    $requete = "update fichefrais set idEtat = '" . $unEtat .

               "', dateModif = now() where idVisiteur ='" .

               $unIdVisiteur . "' and mois = '". $unMois . "'";

    mysqli_query($idCnx, $requete);

    return $requete;

}



/**

 * Contr�le les informations de connexionn d'un utilisateur.

 * V�rifie si les informations de connexion $unLogin, $unMdp sont ou non valides.

 * Retourne les informations de l'utilisateur sous forme de tableau associatif

 * dont les cl�s sont les noms des colonnes (id, nom, prenom, login, mdp)

 * si login et mot de passe existent, le bool�en false sinon.

 * @param resource $idCnx identifiant de connexion

 * @param string $unLogin login

 * @param string $unMdp mot de passe

 * @return array tableau associatif ou bool�en false

 */

function verifierInfosConnexion($idCnx, $unLogin, $unMdp) {

    $unLogin = filtrerChainePourBD($unLogin);

    $unMdp = filtrerChainePourBD($unMdp);

    // le mot de passe est crypt� dans la base avec la fonction de hachage md5

    //admin retire

    $req = "select id, nom, prenom, login, mdp, admin from visiteur where login='".$unLogin."' and mdp='" . $unMdp . "'";

    $idJeuRes = mysqli_query($idCnx ,$req);

    $ligne = false;

    if ( $idJeuRes ) {

        $ligne = mysqli_fetch_assoc($idJeuRes);

        mysqli_free_result($idJeuRes);

    }

    return $ligne;

}











//  function backup() {

//      $file = 'backup_'.@date("Y-m-d-H:i:s").'.gz';

//      $host = "localhost";

//      $db = 'gsb_valide';

//      $user = "root";

//      $pass = "privetik";

//      system(sprintf("/usr/local/mysql/bin/mysqldump --add-drop-table --create-options --skip-lock-tables --extended-insert --quick --set-charset --host=$host --user=$user --password=$pass  $db | gzip > tmp/$file"));

//

//   }

//

//   function restaure() {

//

//    }

//?>

