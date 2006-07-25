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
  <?php mbLoadScript("lib/rico/rico.js"); ?>
</head>

<?php $dialog = dPgetParam( $_GET, 'dialog'); ?>

<body onload="main()">

<script type="text/javascript">
function popChgPwd() {
  var url = new Url;
  url.setModuleAction("admin", "chpwd");
  url.popup(400, 300, "ChangePassword");
}
</script>

<?php 
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
        <td rowspan="3" class="logo">
          <img src="./style/<?php echo $uistyle ?>/images/tonkin.gif" alt="Groupe Tonkin" />
        </td>
        <th width="1%">
          <?php
            $titleBlock = new CTitleBlock("module-$m-long", "$m.png", $m, "$m.$a");
            $titleBlock->addCell();
            $titleBlock->show();
          ?>
        </th>
        <td width="100%">
          <div id="systemMsg">
          <?php
            echo $AppUI->getMsg();
          ?>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="2" id="menubar1">
          <?php
            echo "| ".mbPortalLink( $m, "Aide" )." | ";
          ?>
          <a href="javascript:popChgPwd();">Changez votre mot de passe</a> |
          <a href="?logout=-1"><?php echo $AppUI->_('Logout'); ?></a> |
        </td>
      </tr>
      <tr>
        <td colspan="2" id="menubar2">
          <?php
            foreach ($nav as $module) {
              $modDirectory = $module['mod_directory'];
              if (isMbModuleVisible($modDirectory)) {
                $modName = $AppUI->_($module['mod_ui_name']);
                $textClass = $modDirectory == $m ? "class='textSelected'" : "class='textNonSelected'";
                echo "<a href='?m=$modDirectory' $textClass>" .
                $AppUI->_("module-".$modDirectory."-court") .
                "</a> |\n";
              }
            }
          ?>
        </td>
      </tr>
    </table>
  </td>
</tr>
<tr>
	<td id="menubar">
	</td>
</tr>
</table>

<?php } else { /* (!$dialog) */ ?>
<div id="systemMsg" style="display: block;">
<?php echo $AppUI->getMsg(); ?>
</div>
<?php } ?>

<table id="main" class="<?php echo $m ?>">
<tr>
  <td>
