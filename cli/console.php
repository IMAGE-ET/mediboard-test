#!/usr/bin/php
<?php

/**
 * $Id$
 *  
 * @category CLI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

// CLI or die
PHP_SAPI === "cli" or die;

require __DIR__."/../classes/vendor/autoload.php";
require __DIR__."/autoload.php";

use Symfony\Component\Console\Application;

// Change output encoding for Windows ...
if (defined("PHP_WINDOWS_VERSION_MAJOR")) {
  exec("CHCP 1252");
}

$application = new Application();
$application->add(new DeploySwitchBranch());
$application->add(new ReleaseMakeXML());
$application->add(new DeployMeP());
$application->add(new DeployMeQ());
$application->add(new DeployMaJ());
$application->add(new ESReindex());
$application->add(new CacheCleanup());
$application->run();
