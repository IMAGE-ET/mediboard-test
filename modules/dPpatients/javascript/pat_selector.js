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
    if (id) {
      if (this.eView) {
        this.eView.Value = view;
      }

      this.eId.value = id;
      Try.these(this.eId.onchange.bind(this.eId));
    }
  }
}
