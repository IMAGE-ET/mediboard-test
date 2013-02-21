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
    var url = new Url('cabinet', 'macro_stats');
    url.addElement(form.period);
    url.addElement(form.date);
    url.addElement(form.type_stats);
    url.requestModal(950, 600);
  },
  
  checkParams: function() {
    new Url('cabinet', 'check_params').requestModal(950);
  }
  
}