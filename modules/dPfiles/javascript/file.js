File = {
  use_mozaic : 0,

  popup: function(object_class, object_id, element_class, element_id, sfn) {
    var url = new Url;
    url.ViewFilePopup(object_class, object_id, element_class, element_id, sfn);
  },
    
  upload: function(object_class, object_id, file_category_id){
    var url = new Url("dPfiles", "upload_file");
    url.addParam("object_class", object_class);
    url.addParam("object_id", object_id);
    url.addParam("object_guid", object_class+"-"+object_id);
    url.addParam("file_category_id", file_category_id);
    url.requestModal(700, 500);
  },
  
  cancel: function(form, object_id, object_class){
    if (confirm($T('CFile-comfirm_cancel'))) {
      $V(form.annule, 1);
      onSubmitFormAjax(form, File.refresh.curry(object_id, object_class, 0));
    }
    return false;
  },

  restore: function(form, object_id, object_class) {
    return onSubmitFormAjax(form, File.refresh.curry(object_id, object_class, 0));
  },

  remove: function(oButton, object_id, object_class){
    var oOptions = {
      typeName: 'le fichier',
      objName: oButton.form._view.value,
      ajax: 1,
      target: 'systemMsg'
    };
    var oAjaxOptions = {
      onComplete: function() { File.refresh(object_id, object_class); }
    };
    confirmDeletion(oButton.form, oOptions, oAjaxOptions);
  },

  removeAll: function(oButton, object_guid){
    var oOptions = {
      typeName: 'tous les fichiers',
      objName: '',
      ajax: 1,
      target: 'systemMsg'
    };
    
    object_guid = object_guid.split('-');
    var oAjaxOptions = {
      onComplete: function() { File.refresh(object_guid[1], object_guid[0]); } 
    };
    confirmDeletion(oButton.form, oOptions, oAjaxOptions);
  },
  
  refresh: function(object_id, object_class, only_files, show_actions) {
    var div_id = printf("files-%s-%s", object_id, object_class);
    if (!$(div_id)) {
      return;
    }
    var url = new Url("files", "httpreq_widget_files");
    url.addParam("object_id", object_id);
    url.addParam("object_class", object_class);
    url.addParam("mozaic", File.use_mozaic);
    if (!Object.isUndefined(show_actions)) {
      url.addParam("show_actions", show_actions);
    }

    if (only_files == undefined || only_files == 1) {
      url.addParam("only_files", 1);
      var elt = $("list_"+object_class+object_id);
      var target = "list_"+object_class+object_id;
    }
    else {
      var elt = $("files-"+object_id+"-"+object_class);
      var target = "files-"+object_id+"-"+object_class;
    }

    if (elt.up(".name_readonly")) {
      url.addParam("name_readonly", 1);
    }

    url.requestUpdate(target);
  },
  
  register: function(object_id, object_class, container, show_actions) {
    var div = document.createElement("div");
    div.style.minWidth = "200px";
    div.style.minHeight = "50px";
    div.id = printf("files-%s-%s", object_id, object_class);
    $(container).insert(div);
    
    Main.add(function() {
      File.refresh(object_id, object_class, 0, show_actions);
    });
  },

  createMozaic : function(context_guid, category_id, callback) {
    var url = new Url("files", "ajax_img_to_document");
    url.addParam("context_guid", context_guid);
    url.addParam("category_id", category_id);
    url.requestModal("1024", "768");
    url.modalObject.observe("afterClose", function() {
      if (callback) {
        callback();
      }
      else {
        var parts = context_guid.split("-");
        File.refresh(parts[1], parts[0]);
      }
    });
  },
  
  editNom: function(guid) {
    var form = getForm("editName-"+guid);
    $("readonly_"+guid).toggle();
    $("buttons_"+guid).toggle();
    var input = form.file_name;

    if ($(input).getStyle("display") == "inline-block") {
      $(input).setStyle({display: "none"});
      $V(input, input.up().previous().innerHTML);
    }
    else {
      $(input).setStyle({display: "inline-block"});
      // Focus et sélection de la sous-chaîne jusqu'au dernier point
      input.focus();
      input.caret(0, $V(input).lastIndexOf("."));
    }
  },
  
  toggleClass: function(element) {
    if (element.hasClassName("edit")) {
      element.removeClassName("edit");
      element.addClassName("undo");
    } else {
      element.removeClassName("undo");
      element.addClassName("edit");
    }
  },
  
  reloadFile: function(object_id, object_class, id) {
    var url = new Url("dPfiles", "ajax_reload_line_file");
    url.addParam("id", id);
    url.addParam("dialog", 1);
    url.addParam("object_id", object_id);
    url.addParam("object_class", object_class);
    var elt = $("list_"+object_class+object_id);
    if (elt.up().up().hasClassName("name_readonly")) {
      url.addParam("name_readonly", 1);
    }
    
    url.requestUpdate("td_CFile-"+id);
  },
  
  checkFileName: function(file_name) {
    if (file_name.match(/[\/\\\:\*\?\"<>]/g)) {
      alert("Le nom du fichier ne doit pas comporter les caractères suivants : / \\ : * ? \" < >");
      return false;
    }
    return true;
  },
  
  switchFile: function(id, form, event) {
    if (!event) {
      event = window.event;
    }
    if (Event.key(event) != 9) {
      return true;
    }

    // On annule le comportement par défaut
    if (event.stopPropagation) {
      event.stopPropagation();
    }
    
    if (event.preventDefault) {
      event.preventDefault();
    }
    
    event.returnValue = false;
    
    if (File.checkFileName($V(form.file_name))) {
      form.onsubmit();
      var current_tr = $("tr_CFile-"+id);
  
      // S'il y a un fichier suivant, alors on simule le onclick sur le bouton de modification
      if (next_tr = current_tr.next()) {
        var button = next_tr.down(".edit");
        // Si le bouton d'édition n'existe pas, alors on focus sur l'input pour le changement de nom
        if (button == undefined) {
          var input = next_tr.select("input[type='text']")[0];
          input.focus();
          input.caret(0, $V(input).lastIndexOf("."));
        } else {
          button.onclick();
        }
      }
    }

    return false;
  },

  showCancelled: function(button, table) {
    table.select("tr.file_cancelled").invoke("toggle");
  }
};
