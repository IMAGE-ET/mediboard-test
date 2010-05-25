/* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7795 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

Repartition = {
  updateTechnicien: function(technicien_id) {
    new Url('ssr', 'ajax_sejours_technicien') .
      addParam('technicien_id', technicien_id) . 
      requestUpdate('sejours-technicien-'+technicien_id);
  },
  
  // Make technicien droppable
  droppableTechnicien: function(technicien) {
    Droppables.add("technicien-"+technicien, { 
      onDrop: Repartition.dropSejour,
      hoverclass:'dropover'
    });
  },
  
  // Launch initial plateau update
  registerTechnicien: function (technicien_id) {
    Main.add(Repartition.updateTechnicien.curry(technicien_id));
    Repartition.droppableTechnicien(technicien_id)
  },
  
	// Make sejour draggable
	draggableSejour: function(sejour_guid) {
		new Draggable(sejour_guid, {revert: true, scroll: window})
	},
	
	// Link séjour to kiné
	dropSejour: function(sejour, technicien) {
    sejour.hide();
		var sejour_id = sejour.id.split("-")[1];
    var technicien_id   = technicien  .id.split("-")[1];
		var former_technicien_id = sejour.up(2).id.split("-")[2];

		var form = document.forms['Edit-CBilanSSR'];
    $V(form.sejour_id, sejour_id);
    $V(form.technicien_id, technicien_id);
		onSubmitFormAjax(form, { onComplete: function() {
      Repartition.updateTechnicien(former_technicien_id);
      Repartition.updateTechnicien(technicien_id);
		} } );
		
	}	
}