// $Id: $

Consultation = {
  edit: function(consult_id) {
    new Url().
      setModuleTab("dPcabinet", "edit_consultation").
      addParam("selConsult", consult_id).
      redirectOpener();
  },
	
  plan: function(consult_id) {
    new Url().
      setModuleTab("dPcabinet", "edit_planning").
      addParam("selConsult", consult_id).
      redirectOpener();
  }
}