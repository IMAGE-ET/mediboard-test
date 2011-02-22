// $Id$

ColorSelector = {
  sForm : null,
  sColor: null,
  sColorView: null,
  options : {
    width : 400,
    height: 300
  },
  
  pop: function() {
    var url = new Url("mediusers", "color_selector");
    url.addParam("color", $V(getForm(this.sForm)[this.sColor]));
    url.popup(this.options.width, this.options.height, "Color selector");
  },
  
  set: function(color) {
    var oForm = getForm(this.sForm);
    if (color) {
      $V(oForm[this.sColor], color);
    }
    $(this.sColorView).style.background = '#' + oForm[this.sColor].value;
  }
};
