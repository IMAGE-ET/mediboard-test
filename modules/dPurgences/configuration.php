<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CConfiguration::register(array(
  "CGroups" => array(
    "dPurgences" => array(
      "CRPU" => array(
        "impose_degre_urgence" => "bool default|0",
        "impose_diag_infirmier" => "bool default|0",
        "impose_motif" => "bool default|0"
      )
    ),
  ),
));
