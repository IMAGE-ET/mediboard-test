<?php 

/**
 * $Id$
 *  
 * @category search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @link     http://www.mediboard.org */

CCanDo::checkAdmin();

// Création du template
$smarty = new CSmartyDP();
$smarty->display("vw_search.tpl");

