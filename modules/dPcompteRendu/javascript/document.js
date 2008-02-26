var Document = {
	// Multiples occurences de la même widget
  suffixes: [],
  
	/**
	 * @param ... A DECRIRE
	 */
  create: function(modele_id, object_id, target_id, target_class) {
    if (!modele_id) {
      return;
    }
    
    url = new Url;
    url.setModuleAction("dPcompteRendu", "edit_compte_rendu");
    url.addParam("modele_id", modele_id);
    url.addParam("object_id", object_id);
 
    if (target_id){
      url.addParam("target_id", target_id);
    }
    
    if(target_class){
      url.addParam("target_class", target_class);
    }
    
    url.popup(700, 700, "Document");
  }, 
  
  edit: function(compte_rendu_id){
    var url = new Url;
    url.setModuleAction("dPcompteRendu", "edit_compte_rendu");
    url.addParam("compte_rendu_id", compte_rendu_id);
    url.popup(700, 700, "Document");  
  },
  
  del: function(form, doc_view) {
  	var oConfirmOptions = {	
  		typeName: 'le document',
  		objName: doc_view,
  		ajax: 1,
  		target: 'systemMsg'
  	}
  	
  	var oAjaxOptions = {
  		onComplete: Document.refreshList
  	}
  	
		confirmDeletion(form, oConfirmOptions, oAjaxOptions);
  },
  
  refreshList: function(object_class, object_id) {
    Console.trace("Refreshing for");
    Console.debug(object_class, "Object class");
    Console.debug(object_id   , "Object id");
  }
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
