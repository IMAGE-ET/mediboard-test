<?php 

/**
 * $Id$
 *  
 * @category Vendor
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

require_once __DIR__.'/symfony/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

// List of namespaces
$namespaces = array(
  'Symfony'   => __DIR__."/symfony",
  'SVNClient' => __DIR__."/svnclient",
);

$loader = new UniversalClassLoader();
$loader->registerNamespaces($namespaces);
$loader->register();
