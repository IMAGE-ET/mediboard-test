var File = {
  popup: function(object_class, object_id, element_class, element_id, sfn) {
    var url = new Url;
    url.ViewFilePopup(object_class, object_id, element_class, element_id, sfn);
  },
    
  upload: function(object_class, object_id, file_category_id){
    var url = new Url("dPfiles", "upload_file");
    url.addParam("object_class", object_class);
    url.addParam("object_id", object_id);
    url.addParam("file_category_id", file_category_id);
    url.requestModal(700, 300);
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
  
  refresh: function(object_id, object_class, only_files) {
  	var div_id = printf("files-%s-%s", object_id, object_class);
  	if (!$(div_id)) {
  	  return;
  	}
    var url = new Url("dPcabinet", "httpreq_widget_files");
    url.addParam("object_id", object_id);
    url.addParam("object_class", object_class);
    if (only_files == undefined || only_files == 1) {
      url.addParam("only_files", 1);
      url.requestUpdate("list_"+object_class+object_id);
    }
    else {
      url.requestUpdate("files-"+object_id+"-"+object_class);
    }
  },
  
  register: function(object_id, object_class, container) {
    var div = document.createElement("div");
    div.style.minWidth = "200px";
    div.style.minHeight = "50px";
    div.id = printf("files-%s-%s", object_id, object_class);
    $(container).insert(div);
    
    Main.add(function() {
      File.refresh(object_id,object_class, 0)
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
      // Focus et s�lection de la sous-cha�ne jusqu'au dernier point
      input.focus();
      input.caret(0, $V(input).lastIndexOf("."));
    }
  },
  
  toggleClass: function(element) {
    if (element.hasClassName("edit")) {
      element.removeClassName("edit");
      element.addClassName("cancel");
    } else {
      element.removeClassName("cancel");
      element.addClassName("edit");
    }
  },
  
  reloadFile: function(object_id, object_class, id) {
    var url = new Url("dPcabinet", "ajax_reload_line_file");
    url.addParam("id", id);
    url.addParam("dialog", 1);
    url.addParam("object_id", object_id);
    url.addParam("object_class", object_class);
    url.requestUpdate("td_CFile-"+id);
  },
  
  checkFileName: function(file_name) {
    if (file_name.match(/[\/\\\:\*\?\"<>]/g)) {
      alert("Le nom du fichier ne doit pas comporter les caract�res suivants : / \\ : * ? \" < >");
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

    // On annule le comportement par d�faut
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
        // Si le bouton d'�dition n'existe pas, alors on focus sur l'input pour le changement de nom
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
  }
};
