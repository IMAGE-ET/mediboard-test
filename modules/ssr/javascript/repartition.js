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
	draggableSejour: function(sejour_guid) {
		new Draggable(sejour_guid, {revert: true, scroll: window})
	},
	
	// Make kine droppable
	droppableKine: function(kine_id) {
	  Droppables.add("kine-"+kine_id, { 
	    onDrop: Repartition.dropSejour,
	    hoverclass:'dropover'
	  });
	},
	
	// Link séjour to kiné
	dropSejour: function(sejour, kine) {
		var sejour_id = sejour.id.split("-")[1];
    var kine_id   = kine  .id.split("-")[1];
		sejour.hide();
    console.debug(sejour_id, "Séjour");
    console.debug(kine_id, "Kiné");

		var form = document.forms['Edit-CBilanSSR'];
    $V(form.sejour_id, sejour_id);
    $V(form.kine_id, kine_id);
		onSubmitFormAjax(form);
		
	}	
}