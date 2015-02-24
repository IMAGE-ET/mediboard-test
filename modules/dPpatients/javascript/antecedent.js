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
    if (callback) {
      url.addParam('callback', callback);
    }

    url.requestModal(700, 400);
  },

  closeTooltip: function(object_guid) {
    $(object_guid+'_tooltip').up('.tooltip').remove();
  },

  duplicate: function(form) {
    $V(form.dosql, 'do_duplicate_antecedent_aed');
    onSubmitFormAjax(form, {onComplete: function(){
      if (onComplete) {
        $V(form.dosql, 'do_antecedent_aed');
      }
    }});
  }
};