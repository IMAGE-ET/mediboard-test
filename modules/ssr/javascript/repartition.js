/* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7795 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

Repartition = {
  updateKine: function(kine_id) {
    new Url('ssr', 'ajax_sejours_kine') .
      addParam('kine_id', kine_id) . 
      requestUpdate('sejours-kine-'+kine_id);
  },
  
  // Make kine droppable
  droppableKine: function(kine_id) {
    Droppables.add("kine-"+kine_id, { 
      onDrop: Repartition.dropSejour,
      hoverclass:'dropover'
    });
  },
  
  // Launch initial plateau update
  registerKine: function (kine_id) {
    Main.add(Repartition.updateKine.curry(kine_id));
    Repartition.droppableKine(kine_id)
  },
  
	// Make sejour draggable
	draggableSejour: function(sejour_guid) {
		new Draggable(sejour_guid, {revert: true, scroll: window})
	},
	
	// Link séjour to kiné
	dropSejour: function(sejour, kine) {
    sejour.hide();
		var sejour_id = sejour.id.split("-")[1];
    var kine_id   = kine  .id.split("-")[1];
		var former_kine_id = sejour.up(2).id.split("-")[2];

		var form = document.forms['Edit-CBilanSSR'];
    $V(form.sejour_id, sejour_id);
    $V(form.kine_id, kine_id);
		onSubmitFormAjax(form, { onComplete: function() {
      Repartition.updateKine(former_kine_id);
      Repartition.updateKine(kine_id);
		} } );
		
	}	
}