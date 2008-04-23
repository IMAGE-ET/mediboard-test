/** A filter funtion, useful */

function Filter (sForm, sModule, sAction, sList, aFields, sHiddenColumn) {
  this.sForm   = sForm;
  this.sModule = sModule;
  this.sAction = sAction;
  this.sList   = sList;
  this.aFields = aFields;
  this.sHiddenColumn = sHiddenColumn;
}

Filter.prototype = {
  submit: function (fieldToSelect) {
    oForm = document.forms[this.sForm];
    url = new Url;
    
    url.setModuleAction(this.sModule, this.sAction);

    this.aFields.each (function (f) {
      if (oForm[f]) {
        url.addParam(f,  $F(oForm[f]));
      }
    });
    
    if (fieldToSelect) {
      var oField = oForm.elements[fieldToSelect];
      
      if (oField) {
        oField.focus();
        oField.select();
      }
    }
    
    if (this.sHiddenColumn) {
      url.addParam("hidden_column",  this.sHiddenColumn);
    }
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
  }
}