Preferences = {
  onSubmitAll: function(form) {
    return onSubmitFormAjax(form, Preferences.refresh);
  },
  
  refresh: function (user_id) {
	this.user_id = user_id || this.user_id;
    var url = new Url('admin', 'edit_prefs');
    url.addParam('user_id', this.user_id);
    url.requestUpdate('edit-preferences');
  },
  
  report: function(key) {
    this.back_url = new Url('admin', 'report_prefs');
    this.back_url.addParam('key', key);
    this.back_url.requestModal(500, 500);
  },
  
  edit: function(pref_id) {
	var url = new Url('admin', 'ajax_edit_pref');
	url.addParam('pref_id', pref_id);
    url.requestModal();
    url.modalObject.observe('afterClose', this.back_url.reloadModal.bind(this.back_url));
  },

  onSubmit: function(form) {
    return onSubmitFormAjax(form, Control.Modal.close);
  },

  confirmDeletion: function(form) {
    var options = {
      typeName: 'preference', 
      objName: $V(form.pref_id) 
    };
    
    confirmDeletion(form, options, Control.Modal.close);    
  }
};
