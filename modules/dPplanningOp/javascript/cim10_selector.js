// $Id: $

var CIM10Selector = {
  sForm : null,
  sView : null,
  sChir : null,
  options : {
    width : 600,
    height: 500
  },

  pop: function() {
    var oForm = document[this.sForm];
    var url = new Url();
    url.setModuleAction("dPplanningOp", "code_selector");
    
    url.addParam("chir", oForm[this.sChir].value);
    url.addParam("type", "cim10");
    
    url.popup(this.options.width, this.options.height, "CIM10 Selector");
  },
  
  set: function(code) {
    var oForm = document[this.sForm];
    oForm[this.sView].value = code;
  }
   
  
}
