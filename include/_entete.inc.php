 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

  <head>

    <title>Intranet du Laboratoire Galaxy-Swiss Bourdin</title>

    <meta http-equiv="content-type" content="text/html; charset=utf-8" />

    <link href="./styles/styles.css" rel="stylesheet" type="text/css" />

    <link rel="icon" type="image/png" href="./images/TransMMPortrait_Head.png" />

    <link href="https://fonts.googleapis.com/css?family=Dosis|Open+Sans" rel="stylesheet">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>

   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

  </head>

  <body>



    <?php

    /**

     * Contient la division pour le sommaire, sujet à des variations suivant la

     * connexion ou non d'un utilisateur, et dans l'avenir, suivant le type de cet utilisateur

     * @todo  RAS

     */



    ?>



  <div id="page">

      <nav class="navbar navbar-default">

  <div class="container-fluid">

    <!-- Brand and toggle get grouped for better mobile display -->

    <div class="navbar-header">

      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">

        <span class="sr-only">Toggle navigation</span>

        <span class="icon-bar"></span>

        <span class="icon-bar"></span>

        <span class="icon-bar"></span>

      </button>

      <img src="./images/logo.jpg" id="logoGSB" alt="Laboratoire Galaxy-Swiss Bourdin" title="Laboratoire Galaxy-Swiss Bourdin" />

    </div>



    <!-- Collect the nav links, forms, and other content for toggling -->

    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

      <!-- <h1>Suivi du remboursement des frais</h1> -->

      <?php

        if (estVisiteurConnecte() ) {

          $idUser = obtenirIdUserConnecte() ;

          $lgUser = obtenirDetailVisiteur($idConnexion, $idUser);

          $nom = $lgUser['nom'];

          $prenom = $lgUser['prenom'];

          $admin = $lgUser['admin'];

      ?>

      <ul class="nav navbar-nav navbar-right">

          <li><a href="cAccueil.php">Accueil</a></li>

        <?php

          if( $admin == 1){

        ?>

          <li><a href="cSuivreLePaiementficheDeFrais.php">Suivre le paiement</a></li>

          <li><a href="cValiderFichesFrais.php">Valider fiche de frais</a></li>

        <?php

          }else{

        ?>

          <li><a href="cSaisieFicheFrais.php">Saisie fiche de frais</a></li>

          <li><a href="cConsultFichesFrais.php">Mes fiches de frais</a></li>

        <?php

          }

        ?>

        <li class="dropdown">

          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">  <?php echo $nom . " " . $prenom ;?> <span class="caret"></span></a>

          <ul class="dropdown-menu">

            <li class = "center"><a>

                <?php

                  if( $admin == 1){

                    echo "Comptable";



                  }else{

                    echo "Visiteur médical";



                  }

                ?>

            </a></li>

            <li role="separator" class="divider"></li>

            <li><a href="cSeDeconnecter.php">Se déconnecter</a></li>

          </ul>

          <?php

            // affichage des éventuelles erreurs déjà détectées

            if ( nbErreurs($tabErreurs) > 0 ) {

                echo toStringErreurs($tabErreurs) ;

            }

          }

          ?>

        </li>

      </ul>

    </div><!-- /.navbar-collapse -->

  </div><!-- /.container-fluid -->

</nav>

