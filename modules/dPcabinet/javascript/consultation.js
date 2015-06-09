// $Id: $

Consultation = {
  edit: function(consult_id, fragment) {
    new Url().
      setModuleTab('cabinet', 'edit_consultation').
      addParam('selConsult', consult_id).
      setFragment(fragment).
      redirectOpener();
  },

  plan: function(consult_id) {
    new Url().
      setModuleTab('cabinet', 'edit_planning').
      addParam('consultation_id', consult_id).
      redirectOpener();
  },

  macroStats: function(button) { 
    var form = button.form;
    var url = new Url('cabinet', 'user_stats');
    url.addElement(form.period);
    url.addElement(form.date);
    url.addElement(form.type);
    url.requestModal(-100, -100);
  },
  
  checkParams: function() {
    new Url('cabinet', 'check_params').requestModal(950);
  },

  openConsultImmediate: function(patient_id, sejour_id, operation_id) {
    new Url("cabinet", "ajax_create_consult_immediate")
      .addParam("patient_id"  , patient_id)
      .addParam("sejour_id"   , sejour_id)
      .addParam("operation_id", operation_id)
      .requestModal(500, 200);
  },

  submitAndCallbackConsultImmediate: function(form, callback) {
    $V(form.callback, callback);
    return onSubmitFormAjax(form);
  }
};
