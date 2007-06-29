// $Id: $

var CIM10Selector = {
  eView : null,
  eChir : null,
  options : {
    width : 600,
    height: 500
  },

  pop: function() {
  
    var url = new Url();
    url.setModuleAction("dPplanningOp", "code_selector");
    
    url.addParam("chir", this.eChir.value);
    url.addParam("type", "cim10");
    
    url.popup(this.options.width, this.options.height, "CIM10 Selector");
  },
  
  set: function(code, type) {
     this.eView.value = code;
  }
   
  
}
