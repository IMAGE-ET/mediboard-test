<?php
  $reason = $_GET["reason"];
  switch($reason) {
    case "bdd" :
      $msg = "La base de données n'est pas accessible.";
      break;
    default :
      $msg = "Le système est désactivé pour cause de maintenance.";
  }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
  <title>Mediboard SIH &mdash; Service inaccessible</title>
  <meta http-equiv="Content-Type" content="text/html;charset=iso-8859-1" />
  <meta name="Description" content="Mediboard: Plateforme Open Source pour les Etablissements de Santé" />
  
  <link rel='shortcut icon' type='image/ico' href='style/e-cap/images/icons/favicon.ico' />
  <link rel='stylesheet' type='text/css' href='style/mediboard/main.css' media='all' />
  <link rel='stylesheet' type='text/css' href='style/e-cap/main.css' media='all' />
  
  <!--[if lt IE 8]>
  <link rel="stylesheet" type="text/css" href="style/mediboard/ie.css" media="all" />
  <![endif]-->
</head>

</head>
<body>

<table class="main" style="padding-top: 30px">
  <tr>
    <td class="button">
      <h1>MEDIBOARD EST INACCESSIBLE POUR LE MOMENT</h1>
      <img src="images/pictures/logo.png" style="border: 1px dotted black;" width="350px" />
      <h2>
        <?php echo $msg; ?>
        <br />
        Merci de ressayer ultérieurement.
      </h2>
    </td>
  </tr>
</table>

</body>
</html>