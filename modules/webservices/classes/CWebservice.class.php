<?php

/**
 * Webservice
 *
 * @category Webservices
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * CWebservice
 */
class CWebservice {
  /**
   * @var array Services
   */
  public $services = array();

  /**
   * Get services classes
   *
   * @param string $class Class name
   *
   * @return array
   */
  public function getServicesClasses($class) {
    $this->services = CApp::getChildClasses($class);

    return $this->services;
  }

  /**
   * Gets the class methods' names
   *
   * @param string $class     The class name or an object instance
   * @param string $top_class Top class name
   *
   * @return array
   */
  public function getClassMethods($class, $top_class = null) {
    $methods = array();
    foreach (get_class_methods($class) as $_method) {
      if (!is_method_overridden($class, $_method)) {
        continue;
      }

      if ($_method  && ($_method != "__construct")) {
        $methods[] = $_method;
      }
    }
    if ($top_class && ($top_class != ($parent_class = get_parent_class($class)))) {
      $methods = array_merge($methods, $this->getClassMethods($parent_class, $top_class));
    }

    return $methods;
  }
}