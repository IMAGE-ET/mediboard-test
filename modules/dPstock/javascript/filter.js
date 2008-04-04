function Filter (sForm, sModule, sAction, sList, aFields) {
  this.sForm   = sForm;
  this.sModule = sModule;
  this.sAction = sAction;
  this.sList   = sList;
  this.aFields = aFields;
}

Filter.prototype = {
  submit: function () {
    oForm = document.forms[this.sForm];
    url = new Url;
    
    url.setModuleAction(this.sModule, this.sAction);

    this.aFields.each (function (f) {
      if (oForm[f]) {
        url.addParam(f,  $F(oForm[f]));
      }
    });
    
    url.requestUpdate(this.sList, { waitingText: null } );
    
    return false;
  },
  
  empty: function (fields) {
    oForm = document.forms[this.sForm];

    if (typeof fields == "string") {
      oForm[fields].value = null;
    } else {
      field.each (function (f) {
        if (oForm[f]) {
          oForm[f].value = null;
        }
      });
    }
    this.submit();
  },
}