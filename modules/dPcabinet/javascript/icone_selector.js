// $Id: $

IconeSelector = {
  sForm   : null,
  sView   : null,
  options : {
    width : 400,
    height: 150
  },

  pop: function() {
    var url = new Url("cabinet", "icone_selector");
    url.popup(this.options.width, this.options.height, "Icone");
  },

  set: function(view) {
    var oForm = getForm(this.sForm);
    
    // Champs text qui contient le nom de l'icone
    $V(oForm[this.sView], view);
    
    // Affichage de l'icone
    $('iconeBackground').src = "./modules/dPcabinet/images/categories/"+view;
  },
  
  popChange: function(consult_id) {
    var url = new Url("cabinet", "change_categorie");
    url.addParam("consult_id", consult_id);
    url.requestModal(400, 100);
  }
};