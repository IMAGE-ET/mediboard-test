<?php

/**
 * Merge domains
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$domain_1_id    = CValue::post("domain_1_id");
$domain_2_id    = CValue::post("domain_2_id");
$incrementer_id = CValue::post("incrementer_id");
$actor_id       = CValue::post("actor_id");
$actor_class    = CValue::post("actor_class");
$tag            = CValue::post("tag");
$libelle        = CValue::post("libelle");

$domain_1 = new CDomain();
$domain_1->load($domain_1_id);

$domain_2 = new CDomain();
$domain_2->load($domain_2_id);

$ds = CSQLDataSource::get("std");

$tag_search =  ($tag == $domain_1->tag) ? $domain_2->tag : $domain_1->tag;

// 1. On change les tags de tous les objets liés à ce domaine
$query = "UPDATE `id_sante400` 
            SET `tag` = REPLACE(`tag`, '$tag_search', '$tag'),
                `last_update` = '". mbDateTime() . "'
            WHERE `tag` LIKE '%$tag_search%';";
$ds->query($query);

// 2. On fusionne les domaines
$domain_1->bind($_POST);
$domain_1->_force_merge = true;
if ($msg = $domain_1->merge(array($domain_2))) {
  CAppUI::stepAjax($msg, UI_MSG_WARNING);
} 

CAppUI::stepAjax("CDomain-merge");

CApp::rip();
