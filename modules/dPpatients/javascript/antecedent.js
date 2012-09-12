Antecedent = {
  remove: function(oForm, onComplete) {
    var oOptions = {
      typeName: 'cet antécédent',
      ajax: 1,
      target: 'systemMsg'
    };
    
    onComplete = onComplete || Prototype.emptyFunction;
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
  
  editAntecedents: function(patient_id, type, callback, antecedent_id){
    var url = new Url("dPpatients", "ajax_edit_antecedents");
    url.addParam("patient_id", patient_id);
    url.addParam("type", type);
    if (antecedent_id) {
      url.addParam("antecedent_id", antecedent_id);
    }
    
    url.modal({
      width: 700,
      height: 400
    });
    url.modalObject.observe("afterClose", function() { if (callback) { callback(); } });
  }
}