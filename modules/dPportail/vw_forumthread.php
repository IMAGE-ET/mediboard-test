<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPportail
 * @version $Revision: $
 * @author Fabien
 */
 
global $AppUI, $can, $m;

$can->needsRead();

// Chargement du thread demandé
$forum_thread_id = mbGetValueFromGetOrSession('forum_thread_id');
$forum_thread = new CForumThread();
$forum_thread->load($forum_thread_id);
if($forum_thread->_id) {
    $forum_thread->loadRefs();
} else {
    $forum_thread->user_id = $AppUI->user_id;
    $forum_thread->date = mbDateTime();
}


// Chargement du theme demandé
$forum_theme_id = mbGetValueFromGet('forum_theme_id');
if (!$forum_theme_id) {
	$forum_theme_id = $forum_thread->forum_theme_id;
}
$forum_theme = new CForumTheme();
$forum_theme->load($forum_theme_id);
if($forum_theme->_id) {
	$forum_theme->loadRefs();
}

// Récupération de la liste des themes
$order = 'title ASC';
$theme = new CForumTheme;
$listThemes = $theme->loadList(null,$order);
$listThreads = array();
foreach($listThemes as &$currTheme) {
    $currTheme->loadRefs();
    
    // Récupération de la liste des threads du theme
    if ($currTheme->_id == $forum_theme_id) {
        $listThreads  = $currTheme->_ref_forum_threads;
    }
}

// chargement des references des threads affichés 
// (necessaire pour l'affichage du nombre de reponses de ceux-ci)
foreach($listThreads as &$currThread) {
	$currThread->loadRefsBack();
}

// Création du template
$smarty = new CSmartyDP();

// passage des listes de themes et de threads
$smarty->assign("listThreads", $listThreads);
$smarty->assign("listThemes", $listThemes);

// passage des données du theme et du thread en cours
$smarty->assign("forum_thread", $forum_thread);
$smarty->assign("forum_theme", $forum_theme);

$smarty->display("vw_forumthread.tpl");
?>
