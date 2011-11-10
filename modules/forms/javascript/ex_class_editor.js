exClassTabs = null;

ExClass = {
  id: null,
  layourEditorReady: false,
	pickMode: false,
	setPickMode: function(active){
		this.pickMode = active;
		getForm('form-grid-layout').setClassName("pickmode", active);
	},
  edit: function(id) {
    this.id = id || this.id;
    var url = new Url("forms", "ajax_edit_ex_class");
    url.addParam("ex_class_id", this.id);
    
    id = this.id;
    url.requestUpdate("exClassEditor", { onComplete: function(){
      if (ExField.latest._id && ExField.latest._ex_class_id == id) {
        ExField.edit(ExField.latest._id);
      }
    }});
  },
  editCallback: function(id) {
    ExClass.edit(id);
    ExClass.refreshList();
  },
  refreshList: function(){
    var url = new Url("forms", "ajax_list_ex_class");
    url.requestUpdate("exClassList");
  },
  setEvent: function(select) {
    var form = select.form;
    var parts = $V(select).split(".");
    $V(form.host_class, parts[0]);
    $V(form.event, parts[1]);
  },
  submitLayout: function(drag, drop) {
    var coord_x = drop.get("x"),
        coord_y = drop.get("y"),
        type    = drag.get("type"),
        form = getForm("form-layout-field");
    
    $(form).select('input.coord').each(function(coord){
      $V(coord.disable(), '');
    });
    
    $V(form.ex_class_field_id, drag.get("field_id"));
    $V(form["coord_"+type+"_x"].enable(), coord_x);
    $V(form["coord_"+type+"_y"].enable(), coord_y);
    
    form.onsubmit();
    
    // source parent
    var oldParent = drag.up();
    
    if (drop.hasClassName("grid")) {
      drop.update(drag);
    }
    else {
      drop.insert(drag);
    }
    
    if (oldParent.hasClassName("grid")) {
      oldParent.update("&nbsp;");
    }
  },
  submitLayoutMessage: function(drag, drop) {
    var coord_x = drop.get("x"),
        coord_y = drop.get("y"),
        type    = drag.get("type").split("_")[1],
        form = getForm("form-layout-message");
    
    $(form).select('input.coord').each(function(coord){
      $V(coord.disable(), '');
    });
    
    $V(form.ex_class_message_id, drag.get("message_id"));
    $V(form["coord_"+type+"_x"].enable(), coord_x);
    $V(form["coord_"+type+"_y"].enable(), coord_y);
    
    form.onsubmit();
    
    // source parent
    var oldParent = drag.up();
    
    if (drop.hasClassName("grid")) {
      drop.update(drag);
    }
    else {
      drop.insert(drag);
    }
    
    if (oldParent.hasClassName("grid")) {
      oldParent.update("&nbsp;");
    }
  },
  submitLayoutHostField: function(drag, drop) {
    var coord_x = drop.get("x"),
        coord_y = drop.get("y"),
        type    = drag.get("type"),
        form = getForm("form-layout-hostfield");
        
    if (!drop.hasClassName("droppable")) return;
    
    $(form).select('input.coord').each(function(coord){
      $V(coord.disable(), '');
    });
    
    $V(form.ex_class_host_field_id, drag.get("field_id") || "");
    $V(form.elements.field, drag.get("field") || "");
    $V(form.elements.ex_group_id, drag.get("ex_group_id") || "");
    $V(form["coord_"+type+"_x"].enable(), coord_x);
    $V(form["coord_"+type+"_y"].enable(), coord_y);
    
    $V(form.elements.callback, "");
    $V(form.del, 0);
      
    // source parent
    var oldParent = drag.up();
      
    // dest = LIST
    if (drop.hasClassName("out-of-grid")) {
      var del = drag.get("field_id");
      
      $V(form.elements.callback, "");
    
      if (del) {
        drag.remove();
        oldParent.update("&nbsp;");
        $V(form.del, 1);
      }
      else {
        return;
      }
    }
    
    // dest = GRID
    else {
      var fromGrid = true;
      
      if (!drag.up(".grid")) {
        fromGrid = false;
        drag = drag.clone(true);
        ExClass.initDraggableHostField(drag);
        drag.setStyle({
          position: "static",
          opacity: 1
        });
      }
      
      drop.update(drag);
      
      if (fromGrid) {
        oldParent.update("&nbsp;");
      }
      
      var id = drag.identify();
      $V(form.elements.callback, "ExClass.setHostFieldId.curry("+id+")");
    }
    
    onSubmitFormAjax(form);
  },
  setHostFieldId: function(element_id, object_id, obj) {
    var drag = $(element_id);
    if (drag) {
      drag.setAttribute("data-field_id", object_id);
    }
  },
  initDraggableHostField: function(d){
    new Draggable(d, {
      revert: 'failure', 
      scroll: window, 
      ghosting: true,
      onStart: function(drag){
        drag.element.addClassName("dragging");
        $$(".out-of-grid").invoke("addClassName", "dropactive");
      },
      onEnd: function(drag){
        drag.element.removeClassName("dragging");
        $$(".out-of-grid").invoke("removeClassName", "dropactive");
      }
    });
  },
  initLayoutEditor: function(){
    $$(".draggable:not(.hostfield)").each(function(d){
      d.observe("mousedown", function(event){
        if (!ExClass.pickMode) return;
        
        Event.stop(event);
        
        var element = Event.element(event);
        if (!element.hasClassName("draggable")) {
          element = element.up(".draggable");
        }
        
        var has = element.hasClassName("picked");
        $$(".draggable.picked").invoke("removeClassName", "picked");
        
        if (!has){
          element.toggleClassName("picked");
        }
      });
			
      new Draggable(d, {
        revert: 'failure', 
        scroll: window, 
        ghosting: true,
        onStart: function(draggable){
          var element = draggable.element;
					
          if (!ExClass.pickMode && element.up(".out-of-grid")) {
            element.up(".group-layout").down(".drop-grid").scrollTo();
          }
					
          $$(".out-of-grid").invoke("addClassName", "dropactive");
        },
        onEnd: function(){
          $$(".out-of-grid").invoke("removeClassName", "dropactive");
        }
      });
    });
    
    $$(".draggable.hostfield").each(ExClass.initDraggableHostField);
    
    /*
    $$(".draggable.hr").each(function(d){
      new Draggable(d, {
        //revert: true, 
        scroll: window, 
        ghosting: true
      });
    });*/
		
		function dropCallback(drag, drop) {
      drag.style.position = ''; // a null value doesn't work on IE
      
      // prevent multiple fields in the same cell
      if (drop.hasClassName('grid') && drop.select('.draggable').length) return;
        
      // grid to trash for ExFields
      if (drop.hasClassName("out-of-grid") && !drag.hasClassName('hostfield')) {
        if (drag.hasClassName('field')) {
          drop = drop.down(".field-list");
        }
        
        if (drag.hasClassName('label')) {
          drop = drop.down(".label-list");
        }
        
        if (drag.hasClassName('message_title')) {
          drop = drop.down(".message_title-list");
        }
        
        if (drag.hasClassName('message_text')) {
          drop = drop.down(".message_text-list");
        }
      }
      
      if (drag.hasClassName('hostfield')) {
        ExClass.submitLayoutHostField(drag, drop);
      }
      else if (drag.hasClassName('field') || drag.hasClassName('label')) {
        ExClass.submitLayout(drag, drop);
      }
      else {
        ExClass.submitLayoutMessage(drag, drop);
      }
    }
    
    $$(".droppable").each(function(drop){
      drop.observe("mousedown", function(event){
        if (!ExClass.pickMode) return;
				
        Event.stop(event);
				
				if (drop.childElements().length) return;
        
				var drag = $$(".picked")[0];
				
				if (!drag) return;
				
				dropCallback(drag, drop);
				drop.insert(drag.removeClassName("picked"));
      });
			
      Droppables.add(drop, {
        hoverclass: 'dropover',
        onDrop: dropCallback
      });
    });
    
    ExClass.layourEditorReady = true;
  }
};

ExField = {
  latest: {},
  saveLatest: function(id, obj) {
    ExField.latest = {};
    if (id) {
      obj._id = id;
      ExField.latest = obj;
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
  editCallback: function(id, obj) {
    // void
  },
  create: function(ex_class_id, ex_group_id) {
    this.edit("0", ex_class_id, null, ex_group_id);
  }
};

ExMessage = {
  edit: function(id, ex_group_id) {
    var url = new Url("forms", "ajax_edit_ex_message");
    url.addParam("ex_message_id", id);
    url.addParam("ex_group_id", ex_group_id);
    url.requestUpdate("exFieldEditor");
  },
  editCallback: function(id, obj) {
    // void
  },
  create: function(ex_group_id) {
    this.edit("0", ex_group_id);
  }
};

ExConcept = ExField;

ExConcept.refreshList =  function(){
  var url = new Url("forms", "ajax_list_ex_concept");
  url.requestUpdate("exConceptList");
};

ExConcept.editCallback = function(id, obj) {
  ExConcept.refreshList();
  ExConcept.edit(id, null, "exClassEditor");
};

ExFieldSpec = {
  options: {},
  edit: function(form){
    var url = new Url("forms", "ajax_edit_ex_field_spec2");
    url.addFormData(form);
    url.addParam("m", "forms"); // needed
    url.addParam("form_name", form.getAttribute("name"));
    url.addParam("context_guid", form.get("object_guid"));
    url.requestUpdate("fieldSpecEditor");
  }
};

ExConstraint = {
  edit: function(id, ex_class_id) {
    var url = new Url("forms", "ajax_edit_ex_constraint");
    url.addParam("ex_constraint_id", id);
    url.addParam("ex_class_id", ex_class_id);
    url.requestUpdate("exConstraintEditor");
  },
  create: function(ex_class_id) {
    this.edit("0", ex_class_id);
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
    var pos = value.indexOf('^');
    if (pos != -1) {
      $V(field, value.replace(/\^/g, ""));
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
      };
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
