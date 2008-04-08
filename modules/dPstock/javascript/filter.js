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
    if (!fields) {
      this.aFields.each (function (f) {
        if (oForm[f]) {
          oForm[f].value = null;
          oForm[f].selectedIndex = 0;
        }
      });
    } else if (typeof fields == "string") {
      if (oForm[fields]) {
        oForm[fields].value = null;
        oForm[fields].selectedIndex = 0;
      }
    } else {
      fields.each (function (f) {
        if (oForm[f]) {
          oForm[f].value = null;
          oForm[f].selectedIndex = 0;
        }
      });
    }
    this.submit();
  },
}