// $Id$

ColorSelector = window.ColorSelector || {
  sForm : null,
  sColor: null,
  sColorView: null,
  bAddSharp: false,
  options : {
    width : 400,
    height: 300
  },
  
  pop: function() {
    var url = new Url("mediusers", "color_selector");
    url.addParam("color", $V(getForm(this.sForm)[this.sColor]));
    url.addParam("add_sharp", this.bAddSharp ? "1" : "0");
    url.popup(this.options.width, this.options.height, "Color selector");
  },
  
  set: function(color) {
    var oForm = getForm(this.sForm);
    if (color) {
      $V(oForm[this.sColor], color);
    }
    
    $(this.sColorView).style.background = (!this.bAddSharp ? '#' : '') + oForm[this.sColor].value;
  }
};