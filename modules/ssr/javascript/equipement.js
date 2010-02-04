/* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7795 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

Equipement = {
	plateau_id : null,
	
  edit: function(id) {
    Console.debug(id, "Editing Equipement");
  },
  
  refresh: function() {
    Console.debug(this.plateau_id, "Show equipment list for plateau ");
	},
	
  onSubmit: function(form) {
    Console.debug(getForm(oForm).serialize(true), "Submiting Equipement");
    return false;
  }
} 