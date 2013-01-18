// $Id: plage_selector.js 6447 2009-06-22 08:11:48Z phenxdesign $

Operation = {
  edit: function(operation_id, plage_id) {
    new Url("dPplanningOp", plage_id ? "vw_edit_planning" : "vw_edit_urgence", "tab").
      addParam("operation_id", operation_id).
      redirectOpener();
  },
  
  editModal: function(operation_id, plage_id) {
    var url = new Url("dPplanningOp", plage_id ? "vw_edit_planning" : "vw_edit_urgence", "action");
    url.addParam("operation_id", operation_id);
    url.addParam("dialog", 1);
    url.modal({width: 1000, height: 700});
  },
  
  dossierBloc: function(operation_id, callback) {
    var url = new Url("salleOp", "ajax_vw_operation", "action");
    url.addParam("op", operation_id);
    url.modal({width: 1000, height: 700});
    if(callback) {
      url.modalObject.observe("afterClose", callback);
    }
  },
  
  useModal: function() {
    this.edit = this.editModal;
  },
	
  print: function(operation_id) {
    new Url("dPplanningOp", "view_planning").
      addParam("operation_id", operation_id).
      popup(700, 550, "Admission");
  }
};
