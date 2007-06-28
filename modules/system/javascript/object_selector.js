// $Id: $

var ObjectSelector = {
  eId : null,
  eView : null,
  eClass : null,
  options : {
    width : 600,
    height: 300
  },
    
  pop: function() {
    
    var url = new Url();
    url.setModuleAction("system", "object_selector");
    
    url.addParam("selClass", this.eClass.value);
    url.popup(this.options.width, this.options.height, "Object Selector");
    
    
    
  },
  
  
  set: function(oObject) {
     if (this.eView) {
       this.eView.value = oObject.view;
     }
     this.eClass.value = oObject.objClass;
     this.eId.value = oObject.id;
     
     if(this.eId.onchange){
        this.eId.onchange();
      }
      
  }
   
  
}
