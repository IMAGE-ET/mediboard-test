// $Id: $

{{$_code->code}} = {
	
  edit: function(consult_id) {
    new Url().
      setModuleTab("dPcabinet", "edit_consultation").
      addParam("consult_id", consult_id).
      redirectOpener();
  },
	
  plan: function(consult_id) {
    new Url().
      setModuleTab("dPcabinet", "edit_planning").
      addParam("selConsult", consult_id).
      redirectOpener();
  }
}