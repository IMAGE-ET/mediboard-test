<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Portail
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$forum_theme_id = CValue::getOrSession('forum_theme_id');

// Chargement du theme demandé
$forum_theme = new CForumTheme();
$forum_theme->load($forum_theme_id);
if ($forum_theme->_id) {
  $forum_theme->loadRefs();
}

$order = 'title ASC';
// Récupération de la liste des themes
$theme = new CForumTheme;
$listThemes = $theme->loadList(null, $order);
foreach ($listThemes as $currTheme) {
  $currTheme->loadRefs();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('listThemes', $listThemes);
$smarty->assign('forum_theme', $forum_theme);

$smarty->display('vw_forumtheme.tpl');
