<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprim21
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$echg_hprim21_id = CValue::get("echange_hprim21_id");

$echg_hprim21 = new CEchangeHprim21;
$echg_hprim21->load($echg_hprim21_id);

$message = $echg_hprim21->message;
header("Content-Disposition: attachment; filename={$echg_hprim21->nom_fichier}");
header("Content-Type: text/plain; charset=".CApp::$encoding);
header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
header( "Cache-Control: post-check=0, pre-check=0", false );
header("Content-Length: ".strlen($message));

echo $message;

