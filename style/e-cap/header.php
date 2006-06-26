<?php /* $Id: header.php 15 2006-05-04 14:16:39Z MyttO $ */ ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Mediboard :: Système de gestion des structures de santé</title>
  <meta http-equiv="Content-Type" content="text/html;charset=<?php echo isset( $locale_char_set ) ? $locale_char_set : 'UTF-8';?>" />
  <meta name="Description" content="Mediboard: Plateforme Open Source pour les Etablissement de Santé" />
  <meta name="Version" content="<?php echo @$AppUI->getVersion();?>" />
  <?php mbLinkShortcutIcon("style/$uistyle/images/favicon.ico"); ?>
  <?php mbLinkStyleSheet("style/mediboard/main.css"); ?>
  <?php mbLinkStyleSheet("style/$uistyle/main.css"); ?>
  <?php mbLoadScript("includes/javascript/gosu/array.js"); ?>
  <?php mbLoadScript("includes/javascript/gosu/cookie.js"); ?>
  <?php mbLoadScript("includes/javascript/gosu/debug.js"); ?>
  <?php mbLoadScript("includes/javascript/gosu/ie5.js"); ?>
  <?php mbLoadScript("includes/javascript/gosu/keyboard.js"); ?>
  <?php mbLoadScript("includes/javascript/gosu/string.js"); ?>
  <?php mbLoadScript("includes/javascript/gosu/validate.js"); ?>
  <?php mbLoadScript("includes/javascript/functions.js"); ?>
  <?php mbLoadScript("includes/javascript/cjl_cookie.js"); ?>
  <?php mbLoadScript("includes/javascript/url.js"); ?>
  <?php mbLoadScript("includes/javascript/forms.js"); ?>
  <?php mbLoadScript("includes/javascript/printf.js"); ?>
  <?php mbLoadScript("lib/jscalendar/calendar.js"); ?>
  <?php mbLoadScript("lib/jscalendar/lang/calendar-fr.js"); ?>
  <?php mbLoadScript("lib/jscalendar/calendar-setup.js"); ?>
  <?php mbLoadScript("lib/scriptaculous/lib/prototype.js"); ?>
  <?php mbLoadScript("lib/scriptaculous/src/scriptaculous.js"); ?>
</head>

<body onload="main()">

<script language="javascript">
function popChgPwd() {
  window.open( './index.php?m=admin&a=chpwd&dialog=1', 'chpwd', 'top=250,left=250,width=350, height=220, scollbars=false' );
}
</script>

<?php 
	$dialog = dPgetParam( $_GET, 'dialog');
	if (!$dialog) {
		// top navigation menu
		$nav = $AppUI->getMenuModules();
?>

<?php 
  require_once($AppUI->getModuleClass("system", "message"));
  $messages = new CMessage();
  $messages = $messages->loadPublications("present");
  foreach ($messages as $message) {
    echo "<div style='background: #aaa; color: #fff;'><strong>$message->titre</strong> : $message->corps</div>";
  }
?>

<table id="header" cellspacing="0">
<tr>
  <td id="mainHeader">
    <table>
      <tr>
        <td class="logo">
          <img src="./style/<?php echo $uistyle ?>/images/e-cap.jpg" alt="eCap logo" />
        </td>
        <td class="welcome">
          CAPIO Santé
          <br />
          <?php echo $AppUI->_('Welcome') . " $AppUI->user_first_name $AppUI->user_last_name"; ?>
        </td>
      </tr>
    </table>
  </td>
</tr>
<tr>
	<td id="menubar">
<?php
echo "| ".mbPortalLink( $m, "Aide" )." |";
foreach ($nav as $module) {
  $modDirectory = $module['mod_directory'];
  if (isMbModuleVisible($modDirectory)) {
    $modName = $AppUI->_($module['mod_ui_name']);
    $textClass = $modDirectory == $m ? "class='textSelected'" : "class='textNonSelected'";
    echo "<a href='?m=$modDirectory' $textClass>$modName</a> |\n";
  }
}
?>
    <a href='#' onclick='popChgPwd();return false'>Changez votre mot de passe</a> |
    <a href="./index.php?logout=-1"><?php echo $AppUI->_('Logout');?></a> |
	</td>
</tr>
</table>
<?php } // (!$dialog) ?>
<table id="main" class="<?php echo $m ?>">
<tr>
  <td>
  <div id="systemMsg">
    <?php
	    echo $AppUI->getMsg();
    ?>
  </div>
