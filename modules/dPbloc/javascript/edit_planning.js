
EditPlanning  = {
	status_images : ["images/icons/status_red.png", "images/icons/status_orange.png", "images/icons/status_green.png"],
	modal: null,
	
	edit: function(plageop_id, date) {
		var url = new Url('dPbloc', 'inc_edit_planning');
		url.addParam('plageop_id', plageop_id);
		url.addParam('date', date);
		url.requestModal(800);
		this.modal = url.modalObject;
	},
	
	onSubmit: function(form) {
		return onSubmitFormAjax(form, { 
			onComplete: function() {
			EditPlanning.refreshList();
			EditPlanning.modal.close();
			}
		})
	},
	
	refreshList: function() {
		var url = new Url('dPbloc', 'inc_edit_planning');
		url.requestUpdate('modif_planning');
	},
  
	resfreshImageStatus : function(element){
		if (!element.get('id')) {
			return;
		}
	
		element.title = "";
		element.src   = "style/mediboard/images/icons/loading.gif";
		
		url.addParam("source_guid", element.get('guid'));
		url.requestJSON(function(status) {
			element.src = EditPlanning.status_images[status.reachable];
			});
	}
};