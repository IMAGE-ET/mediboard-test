// $Id: plage_selector.js 6447 2009-06-22 08:11:48Z phenxdesign $

Operation = {
  edit: function(operation_id, plage_id) {
    new Url("dPplanningOp", plage_id ? "vw_edit_planning" : "vw_edit_urgence", "tab").
      addParam("operation_id", operation_id).
      redirectOpener();
  },
	
  print: function(operation_id) {
    new Url("dPplanningOp", "view_planning").
      addParam("operation_id", operation_id).
      popup(700, 550, "Admission");
  }
};
