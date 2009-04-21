// $Id$

var ColorSelector = {
  sForm : null,
  sColor: null,
  options : {
    width : 320,
    height: 250
  },
  
  pop: function() {
    var url = new Url();
    url.setModuleAction("mediusers", "color_selector");
    url.addParam("color", $V(document.forms[this.sForm][this.sColor]));
    url.popup(this.options.width, this.options.height, "Color selector");
  },
  
  set: function(color) {
    oForm = document[this.sForm];
    if (color) {
      $V(oForm[this.sColor], color);
    }
    $('select_color').style.background = '#' + oForm[this.sColor].value;
    
  }
}
