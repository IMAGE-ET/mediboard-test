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

$d1_id          = CValue::post("domain_1_id");
$d2_id          = CValue::post("domain_2_id");
$incrementer_id = CValue::post("incrementer_id");
$actor_id       = CValue::post("actor_id");
$actor_class    = CValue::post("actor_class");
$tag            = CValue::post("tag");
$libelle        = CValue::post("libelle");

$d1 = new CDomain();
$d1->load($d1_id);
$d1->isMaster();

$d2 = new CDomain();
$d2->load($d2_id);
$d2->isMaster();

$and = null;
if ($d1->_is_master_ipp || $d2->_is_master_ipp) {
  $and .= "AND object_class = 'CPatient'";
}
if ($d1->_is_master_nda || $d2->_is_master_nda) {
  $and .= "AND object_class = 'CSejour'";
}

$ds = CSQLDataSource::get("std");

$tag_search =  ($tag == $d1->tag) ? $d2->tag : $d1->tag;

// 1. On change les tags de tous les objets liés à ce domaine
$query = "UPDATE `id_sante400` 
            SET `tag` = REPLACE(`tag`, '$tag_search', '$tag'),
                `last_update` = '". mbDateTime() . "'
            WHERE `tag` LIKE '%$tag_search%'
            $and;";
$ds->query($query);

// 2. On fusionne les domaines
$d1->bind($_POST);
$d1->_force_merge = true;
if ($msg = $d1->merge(array($d2))) {
  CAppUI::stepAjax($msg, UI_MSG_WARNING);
} 

CAppUI::stepAjax("CDomain-merge");

CApp::rip();
