<?php 

/**
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

//Chargement des librairies
CCronJob::loadLibrary();

$now = CMbDT::dateTime();

$cronjob         = new CCronJob();
$cronjob->active = "1";
/** @var CCronJob[] $cronjobs */
$cronjobs = $cronjob->loadMatchingList();

$server_addr = get_server_var("SERVER_ADDR");

//Parcours des tâches actives
foreach ($cronjobs as $_cronjob) {

  if ($_cronjob->_servers && !in_array($server_addr, $_cronjob->_servers)) {
    continue;
  }

  //Récupération de la prochaine date d'éxécution
  $next      = $_cronjob->getNextDate(1);
  $next      = current($next);
  $tolerance = CMbDT::dateTime("+ 5 SECOND", $next);

  //On vérifie si le script doit être éxécuté
  if ($next <= $now && $now <= $tolerance) {
    //Log d'attente
    $cronjob_log                 = new CCronJobLog();
    $cronjob_log->start_datetime = $now;
    $cronjob_log->cronjob_id     = $_cronjob->_id;
    $cronjob_log->status         = "started";
    $cronjob_log->server_address = $server_addr;
    $cronjob_log->store();
    $_cronjob->_params["execute_cron_log_id"] = $cronjob_log->_id;

    //Lancement du processus fils pour éxécuter la requête
    $url = escapeshellarg($_cronjob->makeUrl());
    //pclose(popen("start /SEPARATE wget.exe $url -qO NUL", "r"));
    exec("wget -q $url &> /dev/null");
  }
}
