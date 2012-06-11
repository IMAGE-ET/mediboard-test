Antecedent = {
  remove: function(oForm, onComplete) {
    var oOptions = {
      typeName: 'cet antécédent',
      ajax: 1,
      target: 'systemMsg'
    };
    
    var oOptionsAjax = {
      onComplete: onComplete
    };
    
    confirmDeletion(oForm, oOptions, oOptionsAjax);
  },
  
  cancel: function(oForm, onComplete) {
    $V(oForm.annule, 1);
    onSubmitFormAjax(oForm, {
      onComplete: function(){
        if (onComplete) {
          onComplete();
        }
      }
    });
    $V(oForm.annule, '');
  },
  
  restore: function(oForm, onComplete) {
    $V(oForm.annule, '0');
    onSubmitFormAjax(oForm, {onComplete: function(){
      if (onComplete) {
        onComplete();
      }
    }});
    $V(oForm.annule, '');
  },

  toggleCancelled: function(list) {
    $(list).select('.cancelled').invoke('toggle');
  },
  
  editAntecedents: function(patient_id, type, callback){
    var url = new Url("dPpatients", "ajax_edit_antecedents");
    url.addParam("patient_id", patient_id);
    url.addParam("type", type);
    url.requestModal(700, 400);
    url.modalObject.observe("afterClose", function() { if (callback) { callback(); } });
  }
}