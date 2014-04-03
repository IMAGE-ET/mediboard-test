<?php 

/**
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */


CCanDo::checkAdmin();

// Création du template
$smarty = new CSmartyDP();

$smarty->display("inc_configure_serveur.tpl");