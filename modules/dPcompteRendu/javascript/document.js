Document = {
  popupSize: {
    width: 950,
    height: 800
  },
  iframe: null,
  modeles_ids: null,
  object_class: null,
  object_id: null,
  unique_id: null,
  
  /**
   * @param ... A DECRIRE
   */
  create: function(modele_id, object_id, target_id, target_class, switch_mode) {
    if (!modele_id) return;
    
    var url = new Url("compteRendu", "edit_compte_rendu");
    url.addParam("modele_id", modele_id);
    url.addParam("object_id", object_id);
 
    if (target_id) {
      url.addParam("target_id", target_id);
    }
    
    if (target_class) {
      url.addParam("target_class", target_class);
    }
    
    if (switch_mode) {
      url.addParam("switch_mode", switch_mode);
    }
    
    var multiple_docs = Preferences.multiple_docs == "0" ? "Document" : null;
    url.popup(Document.popupSize.width, Document.popupSize.height, multiple_docs);
  },

  createPack: function(pack_id, object_id, target_id, target_class, switch_mode) {
    if (!pack_id) return;
    
    var url = new Url("compteRendu", "edit_compte_rendu");
    url.addParam("pack_id", pack_id);
    url.addParam("object_id", object_id);
 
    if (target_id){
      url.addParam("target_id", target_id);
    }
    
    if(target_class){
      url.addParam("target_class", target_class);
    }
    
    if (switch_mode) {
      url.addParam("switch_mode", switch_mode);
    }
    
    var multiple_docs = Preferences.multiple_docs == "0" ? "Document" : null;
    url.popup(Document.popupSize.width, Document.popupSize.height, multiple_docs);
  },
  
  fastMode: function(object_class, modele_id, object_id, unique_id, just_save) {
    if (!modele_id) return;
    
    var url = new Url("compteRendu", "edit_compte_rendu");
    url.addParam("modele_id"   , modele_id);
    url.addParam("object_id"   , object_id);
    url.addParam("target_id"   , object_id);
    url.addParam("target_class", object_class);
    url.addParam("unique_id"   , unique_id);
    if (just_save) {
      url.addParam("force_fast_edit", 1);
    }
    url.addParam("just_save"   , just_save ? 1 : 0);
    url.requestModal(750, 400, {onClose: function() {
      // En mode non fusion et édition rapide de pack
      // A la fermeture de la modale, lancement du modèle suivant
      if (Document.modeles_ids && Document.modeles_ids.length) {
        Document.fastMode(Document.object_class, Document.modeles_ids.shift(), Document.object_id, Document.unique_id, true);
      }
    }});
  },
  
  fastModePack: function(pack_id, object_id, object_class, unique_id, modeles_ids) {
    if (!pack_id) return;
    
    // Mode normal
    if (!modeles_ids) {
      var url = new Url("compteRendu", "edit_compte_rendu");
      url.addParam("pack_id", pack_id);
      url.addParam("object_id", object_id);
      url.addParam("unique_id", unique_id);
      url.requestModal(750, 400);
      return;
    }
    
    // Mode un doc par modèle du pack (lancement du premier modèle)
    Document.modeles_ids  = modeles_ids.split("|");
    Document.object_class = object_class;
    Document.object_id    = object_id;
    Document.unique_id    = unique_id;
    
    Document.fastMode(object_class, Document.modeles_ids.shift(), object_id, unique_id, true);
  },
  
  edit: function(compte_rendu_id) {
    var window_name = "Document";
    if (Preferences.multiple_docs != "0") {
      window_name = "cr_" + compte_rendu_id;
    }
    var url = new Url("compteRendu", "edit_compte_rendu");
    url.addParam("compte_rendu_id", compte_rendu_id);
    url.popup(Document.popupSize.width, Document.popupSize.height, window_name);
  },
  
  del: function(form, doc_view) {
    var oConfirmOptions = { 
      typeName: "le document",
      objName: doc_view,
      ajax: 1,
      target: "systemMsg"
    };
    
    var oAjaxOptions = {
      onComplete: Document.refreshList.curry($V(form.file_category_id), $V(form.object_class), $V(form.object_id))
    };
    
    confirmDeletion(form, oConfirmOptions, oAjaxOptions);
  },
  
  refreshList: function(category_id, object_class, object_id) {
    var selector = printf("div.documents-%s-%s", object_class, object_id);
    $$(selector).each(Document.refresh);
    if (window.loadAllDocs) {
      loadAllDocs();
    }
  },
    
  /**
   * Mode normal|collapse Defaults to normal
   */
  register: function(object_id, object_class, praticien_id, container, mode, options) {   
    if (!object_id || !object_class) return;
    
    options = Object.extend({
      mode: "normal",
      categories: "hide"
    }, options);
    
    mode = mode || "normal";
    
    var element = $(container);
    
    if (!element) {
      console.warn(container+" doesn't exist");
      return;
    }
    
    var div = new Element("div", {style: "min-width:260px;"+((mode != "hide") ? "min-height:50px;" : "")});
    div.className = printf("documents-%s-%s praticien-%s mode-%s", object_class, object_id, praticien_id, mode);
    $(element).insert(div);
    Document.refresh(div, null, 0);
  },
  
  refresh: function(container, oOptions, only_docs) {
    var matches = container.className.match(/documents-(\w+)-(\d+) praticien-(\d*) mode-(\w+)/);

    if (!matches) {
      console.warn(printf("'%s' is not a valid document container", container.className));
      return;
    }
    
    oOptions = Object.extend({
      object_class: matches[1],
      object_id   : matches[2],
      praticien_id: matches[3],
      mode        : matches[4]
    }, oOptions);
    
    var url = new Url("compteRendu", "httpreq_widget_documents");
    url.addParam("object_class", oOptions.object_class);
    url.addParam("object_id"   , oOptions.object_id);
    url.addParam("praticien_id", oOptions.praticien_id);
    url.addParam("mode"        , oOptions.mode);
    
    // When two doc widget with the same args in the same page, the ajax request is down ONCE !!!
    url.addParam("_dummyarg_"  , container.identify()); 
    
    if (only_docs == undefined || only_docs == 1) {
      url.addParam("only_docs", 1);
      url.requestUpdate(container.down("table"));
      return;
    }

    url.requestUpdate(container);
  },
  
  print: function(document_id) {
    var oIframe = Element.getTempIframe();
    var url = new Url("compteRendu", "ajax_get_document_source");
    url.addParam("dialog"         , 1);
    url.addParam("suppressHeaders", 1);
    url.addParam("update_date_print", 1);
    url.addParam("compte_rendu_id", document_id);
    var sUrl = url.make();

    if (Prototype.Browser.IE) {
      oIframe.onload = null;
      oIframe.onreadystatechange = function(){
        if (oIframe.readyState !== "complete") {
          return;
        }
        oIframe.contentWindow.document.execCommand("print", false, null);
        oIframe.onreadystatechange = null;
      }
    }
    else {
      oIframe.onload = function() { window.frames[oIframe.name].print(); };
    }
    oIframe.src = sUrl;
  },
  
  printPDF: function(document_id) {
    var url = new Url("compteRendu", "ajax_pdf");
    url.addParam("suppressHeaders", 1);

    if (this.iframe) {
      this.iframe.remove();
    }

    this.iframe = Element.getTempIframe();
    url.pop(0, 0, "Download PDF", null, null, {
      compte_rendu_id: document_id,
      stream: 1,
      update_date_print: 1}, this.iframe);
  },
  
  printSelDocs: function(object_id, object_class) {
    var url = new Url("compteRendu", "print_select_docs");
    url.addParam("object_id"   , object_id);
    url.addParam("object_class", object_class);
    url.requestModal();
  },
  
  afterUnmerge: function(compte_rendu_id, obj) {
    Document.refreshList(obj.file_category_id, obj.object_class, obj.object_id);
    Document.edit(compte_rendu_id);
  },
  
  removeAll: function(oButton, object_guid){
    var oOptions = {
      typeName: 'tous les documents',
      objName: '',
      ajax: 1,
      target: 'systemMsg'
    };
    
    object_guid = object_guid.split('-');
    var oAjaxOptions = {
      onComplete: Document.refreshList.curry(null, object_guid[0], object_guid[1])
    };
    confirmDeletion(oButton.form, oOptions, oAjaxOptions);
  },

  showCancelled: function(button, table) {
    table.select("tr.doc_cancelled").invoke("toggle");
  }
};

