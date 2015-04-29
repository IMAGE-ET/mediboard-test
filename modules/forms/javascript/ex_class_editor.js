exClassTabs = null;

ExClass = window.ExClass || {
  id: null,
  layourEditorReady: false,
  pickMode: true,
  setPickMode: function(active){
    this.pickMode = active;
    getForm('form-grid-layout').setClassName("pickmode", active);
  },
  edit: function(id, callback) {
    this.id = id || this.id;

    MbObject.edit("CExClass-"+id, {
      onComplete: callback || function(){
        if (ExField.latest._id && ExField.latest._ex_class_id == id) {
          ExField.edit(ExField.latest._id);
        }
      }
    });
  },
  exportObject: function(id) {
    var url = new Url("forms", "export_ex_class", "raw");
    url.addParam("ex_class_id", id);
    url.pop(10, 10, "export");
  },
  uploadSaveUID: function(uid) {
    var uploadForm = getForm("upload-import-file-form");
    
    var url = new Url("forms", "ajax_import_ex_class");
    url.addParam("uid", uid);
    url.requestUpdate("import-steps");

    uploadForm.down(".upload-ok").show();
    uploadForm.down(".upload-error").hide();
  },
  uploadError: function() {
    var uploadForm = getForm("upload-import-file-form");

    uploadForm.down(".upload-ok").hide();
    uploadForm.down(".upload-error").show();
  },
  uploadReset: function() {
    var uploadForm = getForm("upload-import-file-form");
    
    uploadForm.down(".upload-ok").hide();
    uploadForm.down(".upload-error").hide();
  }
};

ExField = window.ExField || {
  latest: {},
  editCallback: function(id, obj) {
    ExField.latest = obj;
    ExField.latest._id = id;

    if (obj._ex_class_id) {
      ExClass.edit(obj._ex_class_id);
    }
  },
  edit: function(id, ex_class_id, target, ex_group_id) {
    if (window.exClassTabs) {
      exClassTabs.setActiveTab("fields-specs");
    }

    var url = new Url("forms", "ajax_edit_ex_field");
    
    url.addParam("ex_field_id", id);
    url.addParam("ex_class_id", ex_class_id);
    url.addParam("ex_group_id", ex_group_id);
    url.requestUpdate(target || "exFieldEditor");
  },
  create: function(ex_class_id, ex_group_id) {
    this.edit("0", ex_class_id, null, ex_group_id);
  }
};

ExList = {
  createInModal: function(){
    var url = new Url("forms", "view_ex_list");
    url.addParam("object_guid", "CExList-0");
    url.addParam("hide_tree", 1);
    url.modal({width: 800, height: 600});
  },
  editInModal: function(ex_list_id){
    var url = new Url("forms", "view_ex_list");
    url.addParam("object_guid", "CExList-"+ex_list_id);
    url.addParam("hide_tree", 1);
    url.modal({width: 800, height: 600});
  }
};

ExMessage = {
  edit: function(id, ex_group_id) {
    if (window.exClassTabs) {
      exClassTabs.setActiveTab("fields-specs");
    }
    var url = new Url("forms", "ajax_edit_ex_message");
    url.addParam("ex_message_id", id);
    url.addParam("ex_group_id", ex_group_id);
    url.requestUpdate("exFieldEditor", function(){
      $$("tr[data-ex_class_message_id="+id+"]")[0].addUniqueClassName("selected");
    });
  },
  editCallback: function(id, obj) {
    if (ExClass.id) {
      ExClass.edit(ExClass.id, function(){
        ExMessage.edit(id, obj.ex_group_id);
      });
    }
  },
  create: function(ex_group_id) {
    this.edit("0", ex_group_id);
  }
};

ExSubgroup = {
  edit: function(id, ex_group_id) {
    if (window.exClassTabs) {
      exClassTabs.setActiveTab("fields-specs");
    }
    var url = new Url("forms", "ajax_edit_ex_subgroup");
    url.addParam("ex_subgroup_id", id);
    url.addParam("ex_group_id", ex_group_id);
    url.requestUpdate("exFieldEditor", function(){
      $$("tr[data-ex_subgroup_id="+id+"]")[0].addUniqueClassName("selected");
    });
  },
  editCallback: function(id, obj) {
    // void
  },
  create: function(ex_group_id) {
    this.edit("0", ex_group_id);
  }
};

ExPicture = {
  edit: function(id, ex_group_id) {
    if (window.exClassTabs) {
      exClassTabs.setActiveTab("fields-specs");
    }
    var url = new Url("forms", "ajax_edit_ex_picture");
    url.addParam("ex_picture_id", id);
    url.addParam("ex_group_id", ex_group_id);
    url.requestUpdate("exFieldEditor", function(){
      $$("tr[data-ex_picture_id="+id+"]")[0].addUniqueClassName("selected");
    });
  },
  editCallback: function(id, obj) {
    // void
  },
  create: function(ex_group_id) {
    this.edit("0", ex_group_id);
  }
};

ExConcept = Object.clone(ExField);

ExConcept.refreshList =  function(){
  var url = new Url("forms", "ajax_list_ex_concept");
  url.requestUpdate("exConceptList");
};

ExConcept.editCallback = function(id, obj) {
  ExConcept.edit(id, null, "exClassEditor");
  ExConcept.refreshList();
};

ExConcept.createInModal = function(id){
  var url = new Url("forms", "view_ex_concept");
  url.addParam("object_guid", "CExConcept-"+(id || 0));
  url.addParam("hide_tree", 1);
  url.modal({width: 800, height: 600});
  
  /*
  var callback = function(){
    var cont = url.modalObject.container;
    var iframe = cont.down('iframe');
    iframe.contentWindow.MbObject.editCallback = function(id){
      iframe.onload = callback;
      iframe.src += "&object_guid=CExConcept-"+id;
    }
  }
  
  url.modal({width: 800, height: 600});
  url.modalObject.observe("onRemoteContentLoaded", callback);*/
};

ExFieldSpec = {
  options: {},
  edit: function(form){
    var form_name = form.getAttribute("name");
    
    // stupid IE hack
    if (Prototype.Browser.IE) {
      form_name = form.cloneNode(false).getAttribute("name");
    }
    
    var url = new Url("forms", "ajax_edit_ex_field_spec2");
    url.addFormData(form);
    url.addParam("m", "forms"); // needed
    url.addParam("form_name", form_name);
    url.addParam("context_guid", form.get("object_guid"));
    url.requestUpdate("fieldSpecEditor");
  }
};

ExConstraint = {
  edit: function(id, ex_class_event_id) {
    var url = new Url("forms", "ajax_edit_ex_constraint");
    url.addParam("ex_constraint_id", id);
    url.addParam("ex_class_event_id", ex_class_event_id);
    url.requestModal(600, 600);
  },
  create: function(ex_class_event_id) {
    this.edit("0", ex_class_event_id);
  },
  editCallback: function(ex_class_event_id) {
    ExClassEvent.edit(ex_class_event_id);
    Control.Modal.close();
  }
};

ExClassEvent = {
  edit: function(id, ex_class_id) {
    var url = new Url("forms", "ajax_edit_ex_class_event");
    url.addParam("ex_class_event_id", id);
    url.addParam("ex_class_id", ex_class_id);
    url.requestUpdate("exClassEventEditor", function(){
      $$("[data-event_id="+id+"]")[0].addUniqueClassName("selected");
    });
  },
  create: function(ex_class_id) {
    this.edit("0", ex_class_id);
  },
  setEvent: function(select) {
    var form = select.form;
    var parts = $V(select).split(".");
    $V(form.host_class, parts[0]);
    $V(form.event_name, parts[1]);
    
    var label = form.down("label[for="+form.name+"_unicity_host] strong");
    if (label) {
      label.update($T(parts[0]));
    }
  }
};

ExConceptSpec = {
  options: {},
  edit: function(form){
    var url = new Url("forms", "ajax_edit_ex_field_spec2");
    url.addFormData(form);
    url.addParam("m", "forms"); // needed
    url.addParam("form_name", form.getAttribute("name"));
    url.addParam("context_guid", form.get("object_guid"));
    url.requestUpdate("ExConcept-spec-editor");
  }
};

ExClassHostField = {
  create: function(element) {
    var ex_group_id = element.get("ex_group_id");
    var ex_class_id = element.get("ex_class_id");
    var host_class  = element.get("host_class");
    var field       = element.get("field");
    var type        = element.get("type");

    var url = new Url();
    url.addParam("@class", "CExClassHostField");
    url.addParam("ex_group_id", ex_group_id);
    url.addParam("host_class", host_class);
    url.addParam("field", field);
    url.addParam("type", type);
    url.requestUpdate(SystemMessage.id, {
      method: "post",
      onComplete: function(){
        ExClass.edit(ex_class_id);
      }
    });
  },
  del: function(element){
    if (!confirm("Souhaitez-vous supprimer cet élément du formulaire ?")) {
      return;
    }

    var ex_class_host_field_id = element.get("field_id");
    var ex_class_id = element.get("ex_class_id");
    var url = new Url();
    url.addParam("@class", "CExClassHostField");
    url.addParam("ex_class_host_field_id", ex_class_host_field_id);
    url.addParam("del", 1);
    url.requestUpdate(SystemMessage.id, {
      method: "post",
      onComplete: function(){
        ExClass.edit(ex_class_id);
      }
    });
  }
};

ExFormula = {
  form: null,
  options: {},
  tokens: [],
  edit: function(ex_field_id){
    var url = new Url("forms", "ajax_edit_ex_formula");
    url.addParam("ex_field_id", ex_field_id);
    url.requestUpdate("fieldFormulaEditor", {onComplete: function(){
      ExFormula.form = getForm("editFieldFormula-"+ex_field_id);
      ExFormula.initTextarea();
    }});
  },
  toggleInsertButtons: function(value, type, field_id){
    value = value && ExFormula.form;
    
    if (!value) {
      $$(".insert-formula").invoke("hide");
    }
    else {
      $$("tr.ex-class-field:not([data-ex_class_field_id='"+field_id+"']) .insert-formula."+type).invoke("show");
    }
  },
  insertText: function(text){
    var field = ExFormula.form._formula;

    field.replaceInputSelection(text);
    
    var value = $V(field);
    var pos = value.indexOf('¤');
    if (pos != -1) {
      $V(field, value.replace(/¤/g, ""));
      field.setInputSelection(pos, pos);
    }
  },
  checkTokens: function() {
    var text = $V(ExFormula.form._formula);
    var re = /\[([^\]]+)\]/g;
    var match, bad = [];
    
    while (match = re.exec(text)) {
      if (ExFormula.tokens.indexOf(match[1].strip()) == -1) {
        bad.push(match[1]);
      }
    }
    
    return bad;
  },
  sumAllFields: function(){
    var field = ExFormula.form._formula;
    if ($V(field) && !confirm("Voulez-vous remplacer la formule actuelle ?")) {
      return false;
    }
    
    var tokens = [];
    ExFormula.tokens.each(function(t){
      tokens.push('['+t+']');
    });
    
    $V(field, tokens.join(' + '));
  },
  
  initTextarea: function(){
    var field = ExFormula.form._formula;
    
    field.observe("keyup", function(){
      var bad = ExFormula.checkTokens();
      var message = $("formula-unknown-fields").hide();
      
      if (bad.length == 0) return;
      
      message.show().down("strong").update('"'+bad.join('", "')+'"');
    });

    // Auto-select entire tokens
    field.observe("click", function(){
      var c = field.getInputSelection();
      var text = $V(field).split("");
      var newC = {start: null, end: null};
      
      // find the beginning
      for (var i = c.start; i >= 0; i--) {
        if (text[i] == '[') {
          newC.start = i;
          break;
        }
        
        if (text[i] == ']') return;
      }
      
      // find the end
      for (var i = c.start; i < text.length; i++) {
        if (text[i] == ']') {
          newC.end = i+1;
          break;
        }
        
        if (text[i] == '[') return;
      }
      
      if (newC.start !== null && newC.end !== null)
        field.setInputSelection(newC.start, newC.end);
    });
  }
};

ExFieldPredicate = {
  edit: function(id, ex_field_id, exclude_ex_field_id, form) {
    var url = new Url("forms", "ajax_edit_ex_field_predicate");
    url.addParam("ex_field_predicate_id", id);
    
    if (ex_field_id) {
      url.addParam("ex_field_id", ex_field_id);
    }

    if (exclude_ex_field_id) {
      url.addParam("exclude_ex_field_id", exclude_ex_field_id);
    }

    if(form) {
      var ex_group_id = $V(form.ex_group_id) || $V(form._ex_group_id);
      if (ex_group_id) {
        url.addParam("ex_group_id", ex_group_id);
      }

      if (id == 0) {
        url.addParam("opener_field_value", form.predicate_id.identify());
        url.addParam("opener_field_view",  form.predicate_id_autocomplete_view.identify());
      }
    }
    
    url.requestModal(600, 300);
    
    return false;
  },
  create: function(ex_field_id, exclude_ex_field_id, form) {
    this.edit("0", ex_field_id, exclude_ex_field_id, form);
  },
  initAutocomplete: function(form, ex_class_id) {
    var url = new Url("forms", "ajax_autocomplete_ex_class_field_predicate");
    url.autoComplete(form.elements.predicate_id_autocomplete_view, null, {
      minChars: 2,
      method: "get",
      select: "view",
      dropdown: true,
      afterUpdateElement: function(field, selected){
        var id = selected.get("id");

        if (!id) {
          $V(field.form.predicate_id, "");
          $V(field.form.elements.predicate_id_autocomplete_view, "");
          return;
        }

        $V(field.form.predicate_id, id);

        if (id) {
          showField(id, selected.down('.name').getText());
        }

        if ($V(field.form.elements.predicate_id_autocomplete_view) == "") {
          $V(field.form.elements.predicate_id_autocomplete_view, selected.down('.view').getText());
        }
      },
      callback: function(input, queryString){
        return queryString + "&ex_class_id=" + ex_class_id;
      }
    });
  }
};

ExFieldProperty = {
  edit: function(id, object_class, object_id, form) {
    var url = new Url("forms", "ajax_edit_ex_field_property");
    url.addParam("ex_field_property_id", id);
    
    if (object_id && object_class) {
      url.addParam("object_class", object_class);
      url.addParam("object_id",    object_id);
    }
    
    if (form && id == 0) {
      url.addParam("opener_field_value", form.predicate_id.identify());
      url.addParam("opener_field_view",  form.predicate_id_autocomplete_view.identify());
    }
    
    url.requestModal(600, 300);
  },
  create: function(object_class, object_id, form) {
    this.edit("0", object_class, object_id, form);
  }
};

ExClassCategory = {
  edit: function(id) {
    var url = new Url("forms", "ajax_edit_ex_class_category");
    url.addParam("category_id", id);
    url.requestModal(600, 300);
  },
  create: function() {
    this.edit("0");
  }
};
