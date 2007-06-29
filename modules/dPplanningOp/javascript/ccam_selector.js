// $Id: $

var CCAMSelector = {
  eView : null,
  eClass : null,
  eChir : null,
  options : {
    width : 600,
    height: 500
  },

  pop: function() {
  
    var url = new Url();
    url.setModuleAction("dPplanningOp", "code_selector");
    
    url.addParam("chir", this.eChir.value);
    url.addParam("object_class", this.eClass.value);
    url.addParam("type", "ccam");
    
    url.popup(this.options.width, this.options.height, "CCAM Selector");
  },
  
  set: function(code, type) {
     this.eView.value = code;
  }
   
  
}
