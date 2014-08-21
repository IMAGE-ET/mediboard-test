<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$dPconfig['index_handlers']['CCronJobIndexHandler'] = '1';

$dPconfig["system"] = array (
  "phone_number_format" => "99 99 99 99 99",
  "reverse_proxy"  => "0.0.0.0",
  "website_url"    => "http://www.mediboard.org",
  "CMessage" => array (
    "default_email_from" => "",
    "default_email_to" => "",
  ),

  "import_firstname" => array(
    "start" => "0",
    "step"  => "100"
  ),
);
