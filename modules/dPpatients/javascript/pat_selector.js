// $Id: $

var PatSelector = {
  eId : null,
  eView : null,
  options : {
    width : 750,
    height: 500
  },
    
  pop: function() {
    var url = new Url();
    url.setModuleAction("dPpatients", "pat_selector");
    url.popup(this.options.width, this.options.height, "Patient");
  },
  
  set: function(id, view) {
    Form.Element.setValue(this.eId, id);
    Form.Element.setValue(this.eView, view);
    Form.Element.setValue(this.eId_easy, id);
    Form.Element.setValue(this.eView_easy, view);
  }
}
