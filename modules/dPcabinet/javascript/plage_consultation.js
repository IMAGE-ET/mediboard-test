/* $Id: plage_consultation.js $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author SARL OpenXtrem
* @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
*/

PlageConsultation  = {
	status_images : ["images/icons/status_red.png", "images/icons/status_orange.png", "images/icons/status_green.png"],
	modal: null,
	
	edit: function(plageconsult_id) {
		var url = new Url('dPcabinet', 'edit_plage_consultation');
		url.addParam('plageconsult_id', plageconsult_id);
		url.requestModal(800);
		this.modal = url.modalObject;
	},
	
	onSubmit: function(form) {
		return onSubmitFormAjax(form, { 
			onComplete: function() {
			PlageConsultation.refreshList();
			PlageConsultation.modal.close();
			}
		})
	},
	
	refreshList: function() {
		var url = new Url('dPcabinet', 'edit_plage_consultation');
		url.requestUpdate('maj_plage');
	},
  
	resfreshImageStatus : function(element){
		if (!element.get('id')) {
			return;
		}
	
		element.title = "";
		element.src   = "style/mediboard/images/icons/loading.gif";
		
		url.addParam("source_guid", element.get('guid'));
		url.requestJSON(function(status) {
			element.src = PlageConsultation.status_images[status.reachable];
			});
	}
};