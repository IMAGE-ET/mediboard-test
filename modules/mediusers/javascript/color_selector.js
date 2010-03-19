// $Id$

ColorSelector = {
  sForm : null,
  sColor: null,
	sColorView: null,
  options : {
    width : 320,
    height: 250
  },
  
  pop: function() {
    var url = new Url("mediusers", "color_selector");
    url.addParam("color", $V(document.forms[this.sForm][this.sColor]));
    url.popup(this.options.width, this.options.height, "Color selector");
  },
  
  set: function(color) {
    oForm = document[this.sForm];
    if (color) {
      $V(oForm[this.sColor], color);
    }
    $(this.sColorView).style.background = '#' + oForm[this.sColor].value;
    
  }
};
