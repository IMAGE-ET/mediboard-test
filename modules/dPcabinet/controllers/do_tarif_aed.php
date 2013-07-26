<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$do = new CDoObjectAddEdit("CTarif", "tarif_id");

// redirection vers la comptabilite dans le cas de la creation d'un nouveau tarif dans la consult
if (isset($_POST["_tab"])) {
  $do->redirect = "m=dPcabinet&tab=".$_POST["_tab"];
}
$do->doIt();
