<?php /* $Id$ */ ?>

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
  <?php mbLoadScript("lib/rico/rico.js"); ?>
</head>

<body onload="main()">
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


<table id="header" cellspacing="0"><!-- IE Hack: cellspacing should be useless -->
<tr>
	<td id="menubar">
		<table>
			<tr>
<?php
foreach ($nav as $module) {
	$modDirectory = $module['mod_directory'];
	if (isMbModuleVisible($modDirectory)) {
		$modName = $AppUI->_($module['mod_ui_name']);
		$modIcon = dPfindImage($module['mod_ui_icon'], $module['mod_directory']);
    $modImage = dPshowImage($modIcon, 48, 48, $modName);
    $liClass = $modDirectory == $m ? "class='selected'" : "";
		echo "<td align='center'><a href='?m=$modDirectory'>$modImage<br />" .
        $AppUI->_("module-".$modDirectory."-court") .
        "</a></td>\n";
	}
}

?>
				<td id="new">
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td id="user">
		<table>
			<tr>
				<td id="userWelcome"><?php echo $AppUI->_('Welcome') . " $AppUI->user_first_name $AppUI->user_last_name"; ?></td>
				<td id="userMenu">
          <?php echo mbPortalLink( $m, "Aide en ligne" );?> |
          <?php echo mbPortalLink( "bugTracker", "Suggérer une amélioration" );?> |
          <a href="./index.php?m=admin&amp;a=viewuser&amp;user_id=<?php echo $AppUI->user_id;?>"><?php echo $AppUI->_('My Info');?></a> |
<?php
  if (!getDenyRead( 'calendar' ) && 0) {
    $now = new CDate();
    $date = $now->format( FMT_TIMESTAMP_DATE );
    $today = $AppUI->_('Today');
    echo "<a href='./index.php?m=calendar&amp;a=day_view&amp;date=$date'>$today</a> |";
  }
?>
          <a href="./index.php?logout=-1"><?php echo $AppUI->_('Logout');?></a>
        </td>
			</tr>
		</table>
	</td>
</tr>
</table>
<?php } // (!$dialog) ?>
<table id="main">
<tr>
  <td>
  <div id="systemMsg">
    <?php
      echo $AppUI->getMsg();
    ?>
  </div>
<?php
if(!$dialog) {
  $titleBlock = new CTitleBlock( "module-$m-long", "$m.png", $m, "$m.$a" );
  $titleBlock->addCell();
  $titleBlock->show();
}
?>
