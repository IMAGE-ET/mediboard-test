<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPurgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */
CConfiguration::register(array(
  "CGroups" => array(
    "dPurgences" => array(
      "CRPU" => array(
        "impose_degre_urgence" => "bool default|0"
      )
    ),
  ),
));