// $Id: $

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

    url.popup(this.options.width, this.options.height, "Color selector");
  },
  
  set: function(color) {
    oForm = document[this.sForm];
    if (color) {
      Form.Element.setValue(oForm[this.sColor], color);
    }
    $('test').style.background = '#' + oForm[this.sColor].value;
   
  }
}
