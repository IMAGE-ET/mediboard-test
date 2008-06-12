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
    url.popup(900, 700, "Document");  
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
  },
  
  reloadInit: function(object_id, object_class, praticien_id){
    var url = new Url;
    url.setModuleAction("dPcompteRendu", "httpreq_widget_documents");
    url.addParam("object_class", object_class); 
    url.addParam("object_id"   , object_id);
    url.addParam("praticien_id", praticien_id);
    Document.suffixes.each( function(suffixe) {
	    url.addParam("suffixe", suffixe);
	    url.make();
	    url.requestUpdate("documents-" + suffixe, { waitingText : null } );
   } );
  }, 
  register: function(object_id, object_class, praticien_id, suffixe){
    document.write('<div id=documents-'+suffixe+'></div>');
    Main.add( function() {
      Document.suffixes.push(suffixe);
      Document.reloadInit(object_id,object_class,praticien_id);
    } );
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
