<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Labo
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$dPconfig["dPlabo"] = array (
  "CCatalogueLabo" => array (
    "remote_name" => "LABO",
    "remote_url"  => "http://localhost/mediboard/modules/dPlabo/remote/catalogue.xml",
  ),
  "CPackExamensLabo" => array (
    "remote_url" => "http://localhost/mediboard/modules/dPlabo/remote/pack.xml",
  ),
  "CPrescriptionLabo" => array (
    "url_ftp_prescription"    => "",
    "url_ws_id_prescription"  => "",
    "pass_ws_id_prescription" => "",
    "login_ftp_prescription"  => "",
    "pass_ftp_prescription"   => "",
  ),
);