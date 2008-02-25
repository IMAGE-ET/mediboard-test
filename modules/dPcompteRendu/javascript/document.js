var Document = {
  create : function(modele_id, object_id, target_id, target_class) {
    url = new Url;
    url.setModuleAction("dPcompteRendu", "edit_compte_rendu");
    url.addParam("modele_id", modele_id);
    url.addParam("object_id", object_id);
    if(target_id){
      url.addParam("target_id", target_id);
    }
    if(target_class){
      url.addParam("target_class", target_class);
    }
    
    url.popup(700, 700, "Document");
  }, 
  
  edit : function(compte_rendu_id){
    var url = new Url;
    url.setModuleAction("dPcompteRendu", "edit_compte_rendu");
    url.addParam("compte_rendu_id", compte_rendu_id);
    url.popup(700, 700, "Document");  
  }
  
  /*
  refresh : function(object_id, object_class){
    var url = new Url;
    url.setModuleAction("dPcompteRendu", "vw_list_documents");
    url.addParam("object_id", object_id);
    url.addParam("object_class", object_class);
    url.requestUpdate("document-"+object_id, { waitingText: null } );
  }
  */
  
};

var DocumentPack = {
  create : function (pack_id, operation_id) {
	  var url = new Url();
	  url.setModuleAction("dPcompteRendu", "edit_compte_rendu");
	  url.addParam("pack_id", pack_id);
	  url.addParam("object_id", operation_id);
	  url.popup(700, 700, "Document");
	}
}
