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
  <?php mbLoadScripts(); ?>
</head>

<body onload="main()">

<?php 
	$dialog = dPgetParam( $_GET, 'dialog');
	if (!$dialog) {
		// top navigation menu
		$nav = CMbModule::getVisible();
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
	<td id="menubar">
		<table>
      <tbody id="menuIcons">
			<tr>
        <td class="noHover" />
<?php
foreach ($nav as $module) {
	if (isMbModuleVisible($module->mod_name)) {
    $modNameCourt = $AppUI->_("module-$module->mod_name-court");
    $modNameCourtEscaped = strtr($modNameCourt, "'", "\'");
    $modNameLong = $AppUI->_("module-$module->mod_name-long");
    $modNameLongEscaped = strtr($modNameLong, "'", "\'");
		$modIcon = "modules/$module->mod_name/images/$module->mod_name.png";
    $tdClass = $module->mod_name == $m ? "class='iconSelected'" : "class='iconNonSelected'";
    echo "\n<td align='center' $tdClass>";
    echo "\n  <a href='?m=$module->mod_name' title='$modNameLongEscaped'>";
    echo "\n    <img src='$modIcon' alt='$modNameCourtEscaped' height='48' width ='48' />";
    echo "\n  </a>";
    echo "\n</td>";
	}
}

?>
			</tr>
      </tbody>
      <tr>
        <td class="noHover">
          <button id="triggerMenu" class="triggerHide" type="button" onclick="flipEffectElementPlus('menuIcons', 'triggerMenu', 'slide');" style="float:left" />
        </td>
<?php
foreach ($nav as $module) {
  if (isMbModuleVisible($module->mod_name)) {
    $modNameCourt = $AppUI->_("module-$module->mod_name-court");
    $modNameLong = $AppUI->_("module-$module->mod_name-long");
    $modNameLongEscaped = strtr($modNameLong, "'", "\'");
    $modNameCourtEscaped = strtr($modNameCourt, "'", "\'");
    $tdClass = $module->mod_name == $m ? "class='textSelected'" : "class='textNonSelected'";
    echo "\n<td align='center' $tdClass  title='$modNameLongEscaped'>";
    echo "\n  <a href='?m=$module->mod_name'>";
    echo "\n    <strong>$modNameCourt</strong>";
    echo "\n  </a>";
    echo "\n</td>";
  }
}

?>
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
<script language="JavaScript" type="text/javascript">
  initEffectClass("menuIcons", "triggerMenu");
</script>
<?php } // (!$dialog) ?>
<table id="main" class="<?php echo $m ?>">
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
