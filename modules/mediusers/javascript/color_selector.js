// $Id: $

var ColorSelector = {
  eColor: null,
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
    if (color) {
      this.eColor.value = color;
    }
    document.getElementById('test').style.background = '#' + this.eColor.value;
  
    this.eColor.onchange();
   
  }
}
