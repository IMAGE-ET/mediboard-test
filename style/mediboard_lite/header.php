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


<table id="header" cellspacing="0"><!-- IE Hack: cellspacing should be useless -->
<tr>
  <td id="menubar">
    <table>
      <tr>
<?php
foreach ($nav as $module) {
  if (isMbModuleVisible($module->mod_name)) {
    $modNameCourt = $AppUI->_("module-$module->mod_name-court");
    $modNameCourtEscaped = strtr($modNameCourt, "'", "\'");
    $modNameLong = $AppUI->_("module-$module->mod_name-long");
    $modNameLongEscaped = strtr($modNameLong, "'", "\'");
    $modIcon = "modules/$module->mod_name/images/$module->mod_name.png";
    $liClass = $module->mod_name == $m ? "class='selected'" : "";
    echo "<td align='center'><a href='?m=$module->mod_name'>" .
        "<img src='$modIcon' alt='$modNameCourtEscaped' height='48' width ='48' />" .
        "<br />" .
        $AppUI->_("module-$module->mod_name-court") .
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
        <td id="userWelcome">
          <form name="ChangeGroup" action="" method="get">
            <?php echo $AppUI->_('Welcome') . " $AppUI->user_first_name $AppUI->user_last_name - "; ?>
            <input type="hidden" name="m" value="<?php echo($m); ?>" />
            <select name="g" onchange="ChangeGroup.submit();">
            <?php
            require_once( $AppUI->getModuleClass("mediusers", "mediusers") );
            $Etablissement = new CMediusers();
            $Etablissement = $Etablissement->loadEtablissement(PERM_EDIT);
            foreach($Etablissement as $key=>$group){
              echo("<option value=\"$key\"");
              if($g==$key) echo(" selected=\"selected\"");
              echo(">".$group->_view."</option>");          
            }
            ?>
            </select>
          </form>
        </td>
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
