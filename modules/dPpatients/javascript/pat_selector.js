// $Id: $

var PatSelector = {
  sForm     : null,
  sFormEasy : null,
  sId       : null,
  sView     : null,
  sId_easy  : null,
  sView_easy: null,
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
    var oForm     = document[this.sForm];
    var oFormEasy = document[this.sFormEasy];
    Form.Element.setValue(oForm[this.sId]           , id);
    Form.Element.setValue(oForm[this.sView]         , view);
    if(oFormEasy) {
      Form.Element.setValue(oFormEasy[this.sId_easy]  , id);
      Form.Element.setValue(oFormEasy[this.sView_easy], view);
    }
  }
}
