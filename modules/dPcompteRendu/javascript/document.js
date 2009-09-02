var Document = {
	/**
	 * @param ... A DECRIRE
	 */
  create: function(modele_id, object_id, target_id, target_class) {
    if (!modele_id) return;
    
    var url = new Url("dPcompteRendu", "edit_compte_rendu");
    url.addParam("modele_id", modele_id);
    url.addParam("object_id", object_id);
 
    if (target_id){
      url.addParam("target_id", target_id);
    }
    
    if(target_class){
      url.addParam("target_class", target_class);
    }
    
    url.popup(800, 700, "Document");
  },
  
  createPack: function(pack_id, object_id, target_id, target_class) {
    if (!pack_id) return;
    
    var url = new Url("dPcompteRendu", "edit_compte_rendu");
    url.addParam("pack_id", pack_id);
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
    var url = new Url("dPcompteRendu", "edit_compte_rendu");
    url.addParam("compte_rendu_id", compte_rendu_id);
    url.popup(900, 700, "Document");  
  },
  
  del: function(form, doc_view) {
  	var oConfirmOptions = {	
  		typeName: 'le document',
  		objName: doc_view,
  		ajax: 1,
  		target: 'systemMsg'
  	};
  	
  	var oAjaxOptions = {
  		onComplete: function () { 
  			Document.refreshList($V(form.object_class), $V(form.object_id)); 
  		}
  	};
  	
		confirmDeletion(form, oConfirmOptions, oAjaxOptions);
  },
  
  refreshList: function(object_class, object_id) {
    var selector = printf("div.documents-%s-%s", object_class, object_id);
  	$$(selector).each(Document.refresh);
  },
    
  /**
   * Mode normal|collapse Defaults to normal
   */
  register: function(object_id, object_class, praticien_id, container, mode, userOptions) {
  	var options = {
  	  mode: "normal",
  	  categories: "hide"
  	};
  	
  	Object.extend(options, userOptions);
  	
  	if (!mode) mode = "normal";
    var div = document.createElement("div");
    div.style.minWidth = "260px";
    if (mode != "hide") div.style.minHeight = "50px";
    
    div.className = printf("documents-%s-%s praticien-%s mode-%s", object_class, object_id, praticien_id, mode);
    $(container).insert(div);
    Main.add( function() {
      Document.refresh(div);
    } );
  },
  
  refresh: function(div) {
    var matches = div.className.match(/documents-(\w+)-(\d+) praticien-(\d*) mode-(\w+)/);
    
	  var url = new Url("dPcompteRendu", "httpreq_widget_documents");
	  url.addParam("object_class", matches[1]);
	  url.addParam("object_id"   , matches[2]);
	  url.addParam("praticien_id", matches[3]);
	  url.addParam("mode"        , matches[4]);
	  url.requestUpdate(div, { waitingText : null } );
  }
};

var DocumentPack = {
  create : function (pack_id, operation_id) {
	  var url = new Url("dPcompteRendu", "edit_compte_rendu");
	  url.addParam("pack_id", pack_id);
	  url.addParam("object_id", operation_id);
	  url.popup(700, 700, "Document");
	}
};
