<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CWebservice {
	var $services = array();
	
	public function getServicesClasses($class) {
		CAppUI::getAllClasses();
    $this->services = getChildClasses($class);
		
		return $this->services;
	}
	
	public function getClassMethods($class, $top_class = null) {	
	  $methods = array();	
		foreach (get_class_methods($class) as $_method) {
			if (is_method_overridden($class, $_method)) {
				if ($_method  && ($_method != "__construct")) {
					$methods[] = $_method;
				}
			}
		}
		if ($top_class && ($top_class != ($parent_class = get_parent_class($class)))) {
			$methods = array_merge($methods, $this->getClassMethods($parent_class, $top_class));
		}
		
		return $methods;
	}
}

?>