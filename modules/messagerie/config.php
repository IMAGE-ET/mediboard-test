<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */


$dPconfig["messagerie"] = array (
  "enable_internal"               => "1",
  "enable_external"               => "1",
  "CronJob_nbMail"                => "5",
  "CronJob_schedule"              => "3",
  "CronJob_olderThan"             => "5",
  "resctriction_level_messages"   => "all",
  "limit_external_mail"           => "20",
);