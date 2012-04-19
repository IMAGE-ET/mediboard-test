AffectationUf  = {
	modal: null,
	
	edit: function(object_guid) {
		var url = new Url('dPhospi'	, 'ajax_affectation_uf');
		url.addParam('object_guid'	, object_guid);
		url.requestModal(450);
		this.modal = url.modalObject;
	},
	
	affecter: function(curr_affectation_guid, lit_guid) {
		var url = new Url('dPhospi'				, 'ajax_vw_association_uf');
		url.addParam('curr_affectation_guid'	, curr_affectation_guid);
		url.addParam('lit_guid'	, lit_guid);
		url.requestModal(600, 400);
		this.modal = url.modalObject;
	},
  
	onSubmit: function(form) {
		Control.Modal.close();
		return onSubmitFormAjax(form);	
	},
  
	onDeletion: function(form) {
		Control.Modal.close();
		return confirmDeletion(form);   
	}
};