// $Id: $

var CCAMSelector = {
  sForm  : null,
  sView  : null,
  sTarif : null,
  sClass : null,
  sChir  : null,
  options : {
    width : 700,
    height: 500
  },

  pop: function() {
    var oForm = document[this.sForm];
    var url = new Url();
    url.setModuleAction("dPplanningOp", "code_selector");
    
    url.addParam("chir"        , oForm[this.sChir].value);
    url.addParam("object_class", oForm[this.sClass].value);
    url.addParam("type"        , "ccam");
    
    url.popup(this.options.width, this.options.height, "CCAM Selector");
  },
  
  set: function(code,tarif) {
    var oForm = document[this.sForm];
    oForm[this.sView].value = code;
    if(this.sTarif != null){
      oForm[this.sTarif].value = tarif;
    }
  }
   
  
}
