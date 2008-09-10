// $Id: $

var ObjectSelector = {
  sForm    : null,
  sId      : null,
  sView    : null,
  sClass   : null,
  onlyclass: null,

  options : {
    width : 600,
    height: 300
  },
   
  pop: function() {
    var oForm = document[this.sForm];
    var url = new Url();
    url.setModuleAction("system", "object_selector");
    url.addParam("onlyclass", this.onlyclass);
    url.addParam("selClass", oForm[this.sClass].value);
    
    url.popup(this.options.width, this.options.height, "Object Selector");
  },
  
  
  set: function(oObject) {
    var oForm = document[this.sForm];
    
    if (oForm[this.sView]) {
      oForm[this.sView].value = oObject.view;
    }
    
    oForm[this.sClass].value = oObject.objClass;
     
    $V(oForm[this.sId], oObject.id);
      
  }
   
  
}
