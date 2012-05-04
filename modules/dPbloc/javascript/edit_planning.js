
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
  
	resfreshImageStatus: function(element){
		if (!element.get('id')) {
			return;
		}
	
		element.title = "";
		element.src   = "style/mediboard/images/icons/loading.gif";
		
		url.addParam("source_guid", element.get('guid'));
		url.requestJSON(function(status) {
			element.src = EditPlanning.status_images[status.reachable];
			});
	},
	
	popPlanning: function(date) {
    var url = new Url("dPbloc", "view_planning");
    url.addParam("_date_min", date);
    url.addParam("_date_max", date);
    url.addParam("salle"    , 0);
    url.popup(900, 550, "Planning");
	},
	
	showAlerte: function(date, bloc_id, type) {
    var url = new Url("dPbloc", "vw_alertes");
    url.addParam("date"   , date);
    url.addParam("type"   , type);
    url.addParam("bloc_id", bloc_id);
    url.requestModal(800, 500);
  },
  
  monitorDaySalle: function(salle_id, date) {
    var url = new Url("bloc", "monitor_day_salle");
    url.addParam("salle_id", salle_id);
    url.addParam("date"    , date);
    url.requestModal(900);
  },
  
  lockPlages: function(form) {
    if(confirm('Voulez-vous verrouiller toutes les plages de ce jour ?')) {
      return checkForm(form);
    }
    return false;
  }
};