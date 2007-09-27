// $Id: $

var IconeSelector = {
  sForm   : null,
  sView   : null,
  options : {
    width : 330,
    height: 70
  },

  pop: function() {
    var oForm = document[this.sForm];
    var url = new Url();
    url.setModuleAction("dPcabinet", "icone_selector");
    url.popup(this.options.width, this.options.height, "Icone");
  },

  set: function(view) {
    var oForm = document[this.sForm];
    
    // Champs text qui contient le nom de l'icone
    oForm[this.sView].value = view;
    oForm[this.sView].onchange();
    
    //Affichage de l'icone
    $('iconeBackground').src = "./modules/dPcabinet/categories/"+view;
  }
}