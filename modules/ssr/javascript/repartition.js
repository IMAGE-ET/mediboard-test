/* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7795 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

Repartition = {
  updatePlateau: function(plateau_id) {
    new Url('ssr', 'ajax_repartition_plateau') .
      addParam('plateau_id', plateau_id) . 
      requestUpdate('repartition-plateau-'+plateau_id);
  },
  
  updateSejours: function() {
    new Url('ssr', 'ajax_sejours_non_repartis') .
      requestUpdate('sejours_non_repartis');
  },

  // Launch initial plateau update
  registerPlateau: function (plateau_id) {
    Main.add(Repartition.updatePlateau.curry(plateau_id));
  },
  
  // Launch initial sejours update
  registerSejours: function () {
    Main.add(Repartition.updateSejours);
  },
	
	// Make sejour draggable
	draggableSejour: function(sejour_id) {
		new Draggable(sejour_id, {revert: true, scroll: window})
	}
}