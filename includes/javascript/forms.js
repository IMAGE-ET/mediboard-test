// $Id$

function addHelp(sClass, oField, sName, sDepend) {
  url = new Url;
  if(!sDepend) {
    sDepend = null;
  }
  url.setModuleAction("dPcompteRendu", "edit_aide");
  url.addParam("class"       , sClass);
  url.addParam("field"       , sName || oField.name);
  url.addParam("text"        , oField.value);
  url.addParam("depend_value", sDepend);
  url.popup(600, 300, "AidesSaisie");
}

function confirmDeletion(oForm, oOptions, oOptionsAjax) {
  oDefaultOptions = {
    typeName: "",
    objName : "",
    msg     : "Voulez-vous réellement supprimer ",
    ajax    : 0,
    target  : "systemMsg"
  }
  
  Object.extend(oDefaultOptions, oOptions);
  
  if (oDefaultOptions.objName.length) oDefaultOptions.objName = " '" + oDefaultOptions.objName + "'";
  if (confirm(oDefaultOptions.msg + oDefaultOptions.typeName + " " + oDefaultOptions.objName + " ?" )) {
  	oForm.del.value = 1;
  	if(oDefaultOptions.ajax)
  	  submitFormAjax(oForm, oDefaultOptions.target, oOptionsAjax);
  	else
  	  oForm.submit();
  }
}

function confirmDeletionOffline(oForm, oFct, oOptions, oOptionsAjax) {
  oDefaultOptions = {
    typeName: "",
    objName : "",
    msg     : "Voulez-vous réellement supprimer ",
    ajax    : 0,
    target  : "systemMsg"
  }
  
  Object.extend(oDefaultOptions, oOptions);
  
  if (oDefaultOptions.objName.length) oDefaultOptions.objName = " '" + oDefaultOptions.objName + "'";
  if (confirm(oDefaultOptions.msg + oDefaultOptions.typeName + " " + oDefaultOptions.objName + " ?" )) {
    oForm.del.value = 1;
    oFct();
  }
}

function getLabelFor(oElement) {
  var aLabels = oElement.form.select("label");
  var iLabel = 0;
  while (oLabel = aLabels[iLabel++]) {
    if (oElement.id == oLabel.htmlFor) {
      return oLabel;
    }
  } 
  
  return null; 
}

/** Universal get/set function for form elements
  * @param element A form element (Form.Element or id) : input, textarea, select, group of radio buttons, group of checkboxes
  * @param value   If set, sets the value to the element. Can be an array of values : ['elementvalue1', 'elementvalue2', ...] 
  * @param fire    Determines wether the onchange callback has to be called or not
  * @return        An array of values for multiple selectable elements, a boolean for 
  *                single checkboxes/radios, a string for textareas and text inputs
  */
$V = function (element, value, fire) {
  if (!element) {
    return;
  }
  element = $(element);
  fire = Object.isUndefined(fire) ? true : fire;
  
  // We get the tag and the type
  var tag  = element.tagName ? element.tagName.toLowerCase() : null;
  var type = element.type    ? element.type.toLowerCase()    : null;
  
  // If it is a form element
  if (Object.isElement(element) && (
     tag == 'input' || 
     tag == 'select' || 
     tag == 'textarea')
    ) {
    
    // If the element is a selectable one, we check if it's checked
    var oldValue = (type == 'checkbox' || type == 'radio') ? element.checked : $F(element);

    // If a value is provided
    if (!Object.isUndefined(value) && value != oldValue) {
      element.setValue(value);
      if (fire) {
        (element.onchange || Prototype.emptyFunction).bind(element)();
      }
    }
    
    // else, of no value is provided
    else {
      return oldValue;
    }
  } 
  
  // If the element is a list of elements (like radio buttons)
  else if (element instanceof NodeList || Object.isArray(element)) {
    if (!Object.isUndefined(value)) { // If a value is provided
    
      // If value isn't an array, we make it an array
      value = Object.isArray(value) ? value : [value];
      
      // For every element, we apply the right value (in an array or not)
      $A(element).each(function(e) { // For every element in the list
        $V(e, value.indexOf(e.value) != -1, fire);
      });
    }
    else { // else, if no value is provided
      var ret = [];
      $A(element).each(function (e) { // For every element in the list
        if ($V(e)) {
          ret.push(e.value);
        }
        type = e.type ? e.type.toLowerCase() : null;
      });
      
      if (type == 'radio') {
        ret = ret.reduce();
      }
      return (ret && ret.length > 0) ? ret : null;
    }
  }
  return;
}

function pasteHelperContent(oHelpElement) {
  var aFound = oHelpElement.name.match(/_helpers_(.*)/);
  Assert.that(aFound.length == 2, "Helper element '%s' is not of the form '_helpers_propname'", oHelpElement.name);
  
  var oForm       = oHelpElement.form; 
  var aFieldFound = aFound[1].split("-");
  
  var sPropName = aFieldFound[0];
  var oAreaField = oForm.elements[sPropName];

  var sValue = oHelpElement.value;
  oHelpElement.value = "";
  insertAt(oAreaField, sValue + '\n')
  oAreaField.scrollTop = oAreaField.scrollHeight;
}

function putHelperContent(oElem, sFieldSelect) {
  var oForm      = oElem.form;
  var sDependsOn = oElem.options[oElem.selectedIndex].value;

  // Search for helpers elements in same form
  for (var i=0; i< oForm.elements.length; i++) {
    var element = oForm.elements[i];
    
    // Filter helper elements
    var aFound = element.name.match(/_helpers_(.*)/);
    if (!aFound) {
    	continue;
    }
    
    Assert.that(aFound.length == 2, "Helper field name '%s' incorrect", element.name);
    Assert.that(element.nodeName == "SELECT", "Helper field name '%s' should be a select", element.name);
    
    
    // Check correspondance
		var aHelperParts = aFound[1].split("-");
		Assert.that(aHelperParts[0] == sFieldSelect, "Helper Field '%s' should target '%s' field",  element.name, sFieldSelect);
    
    // Show/Hide helpers
    var sHelperDependsOn = aHelperParts[1]; 
    if (sHelperDependsOn == "no_enum") {
    	sHelperDependsOn = "";
    }
    
    $(element)[sHelperDependsOn == sDependsOn ? "show" : "hide"]();
  }
}

function notNullOK(oElement) {
  if (oLabel = getLabelFor(oElement)) {
    oLabel.className = oElement.value ? "notNullOK" : "notNull";
  } 
}

function canNullOK(oElement) {
  if (oLabel = getLabelFor(oElement)) {
    oLabel.className = oElement.value ? "notNullOK" : "canNull";
  } 
}

function getBoundingForm(oElement) {
  if (!oElement) {
    return null;
  }
  
  if (oElement.nodeName.match(/^form$/i)) {
    return oElement;
  }
  
  return getBoundingForm(oElement.parentNode);
}

var bGiveFormFocus = true;

var FormObserver = {
  changes        : 0,
  lastFCKChange  : 0,
  fckEditor      : null,
  checkChanges : function() {
    if(this.changes) {
      return false;
    } else {
      return true;
    }
  },
  elementChanged : function() {
    this.changes++;
  },
  FCKChanged : function(timmer) {
    if(this.lastFCKChange < timmer) {
      this.elementChanged();
    }
    this.lastFCKChange = timmer;
  }
}

Element.addMethods({
  setResizable: function (oElement, oOptions) {
    var oDefaultOptions = {
      autoSave: true,
      step: 1
    };
    Object.extend(oDefaultOptions, oOptions);
  
    var staticOffset = null;
    var cookie = new CookieJar(); 
    
    // oGrippie is the draggable element
    var oGrippie = new Element('div');
    
    // We remove the margin between the textarea and the grippie
    oElement.style.marginBottom = '0';
    
    // grippie's class and style
    oGrippie.addClassName('grippie-h');
    oGrippie.setOpacity(0.5);
    if (!oElement.visible()) {
      oGrippie.hide();
    }
    
    // When the mouse is pressed on the grippie, we begin the drag
    oGrippie.onmousedown = startDrag;
    oElement.insert({after: oGrippie});
    
    // Loads the height maybe saved in a cookie
    function loadHeight() {
      if (h = cookie.getValue('ElementHeight', oElement.id)) {
        oElement.setStyle({height: (h+'px')});
      }
    }
    loadHeight.defer(); // deferred to prevent Firefox 2 resize bug
    
    function startDrag(e) {
      staticOffset = oElement.getHeight() - Event.pointerY(e); 
      oElement.setOpacity(0.4);
      document.onmousemove = performDrag;
      document.onmouseup = endDrag;
      return false;
    }
  
    function performDrag(e) {
      var h = null;
      if (typeof oDefaultOptions.step == 'string') {
        var iStep = oElement.getStyle(oDefaultOptions.step);
        iStep = iStep.substr(0, iStep.length - 2);
        
        h = Math.max(iStep*2, staticOffset + Event.pointerY(e)) - Math.round(oGrippie.getHeight()/2);
        h = Math.round(h / iStep)*iStep;
      } else {
        h = Math.max(32, staticOffset + Event.pointerY(e));
      }
      oElement.setStyle({height: h + 'px'});
      return false;
    }
  
    function endDrag(e) {
      oElement.setStyle({opacity: 1});
      document.onmousemove = null;
      document.onmouseup = null;

      if (oElement.id) {
        cookie.setValue('ElementHeight', oElement.id, oElement.getHeight() - Math.round(oGrippie.getHeight()/2));
      }
      return false;
    }
  }
} );

function prepareForm(oForm, bForcePrepare) {
  if (Object.isString(oForm)) {
    oForm = document.forms[oForm];
  }
  oForm = $(oForm);

  // If this form hasn't been prepared yet
  if (!oForm.hasClassName("prepared") || bForcePrepare) {
  
    // Event Observer
    if(oForm.hasClassName("watched")) {
      new Form.Observer(oForm, 1, function() { FormObserver.elementChanged(); });
    }
    // Form preparation
    var sFormName = oForm.getAttribute("name");
    oForm.lockAllFields = (oForm._locked && oForm._locked.value) == "1"; 
  
    // Build label targets
    var aLabels = oForm.select("label");
    var iLabel = 0;
    var oLabel = null;
    var sFor = null;
    while (oLabel = aLabels[iLabel++]) {
      // oLabel.getAttribute("for") is not accessible in IE
      if (sFor = oLabel.htmlFor) {
        if (sFor.indexOf(sFormName) != 0) {
          oLabel.htmlFor = sFormName + "_" + sFor;
        }
      }
    }
  
    // For each element
    var iElement = 0;
    var oElement = null;
    var sPropSpec = null;
    var aSpecFragments = null;
    while (oElement = oForm.elements[iElement++]) {
    	oElement = $(oElement);
    	
    	// Locked object
    	if (oForm.lockAllFields) {
    		oElement.disabled = true;
    	}
    	
      // Create id for each element if id is null
      if (!oElement.id) {
        oElement.id = sFormName + "_" + oElement.name;
        if (oElement.type == "radio") {
          oElement.id += "_" + oElement.value;
        }
      }
      
			// Not null
		  if (oElement.hasClassName("notNull")) {
        notNullOK(oElement);
        Element.addEventHandler(oElement, "change", notNullOK);
      }
      
			// Can null
		  if (oElement.hasClassName("canNull")) {
        canNullOK(oElement);
        Element.addEventHandler(oElement, "change", canNullOK);
      }
      
      // Select tree
      if (oElement.hasClassName("select-tree")) {
        oElement.buildTree();
      }
      
      // Focus on first text input
      if (bGiveFormFocus && oElement.type == "text" && !oElement.getAttribute("readonly")) {
        // Internet Explorer will not give focus to a not visible element but will raise an error
        if (oElement.clientWidth > 0) {
          oElement.focus();
          bGiveFormFocus = false;
        }
      }
      
      // Won't make it resizable on IE
      if (oElement.type == "textarea" && !Prototype.Browser.IE) {
        oElement.setResizable({autoSave: true, step: 'font-size'});
      }      
      
      // We mark this form as prepared
      oForm.addClassName("prepared");
    }
  }
}

function prepareForms() {
  // For each form
  var iForm = 0;
  var oForm = null;
  while (oForm = document.forms[iForm++]) {
    prepareForm(oForm);
  }
}

function submitFormAjax(oForm, ioTarget, oOptions) {
  if (oForm.attributes.onsubmit) {
    if (oForm.attributes.onsubmit.nodeValue) {        // this second test is only for IE
      if (!oForm.onsubmit()) {
        return;
      }
    }  
  }

  var url = new Url;
  var iElement = 0;
  var oElement = null;
  while (oElement = oForm.elements[iElement++]) {
    if ((oElement.type != "radio" && oElement.type != "checkbox") || oElement.checked) {
      url.addParam(oElement.name, oElement.value);
    }
  }

  var oDefaultOptions = {
    method : oForm.method
  };
 
  Object.extend(oDefaultOptions, oOptions);

  url.requestUpdate(ioTarget, oDefaultOptions);
}

/**
 * Submit a form in Ajax mode
 * New version to plage in onsubmit event of the form
 * @param oForm Form element
 * @return false to prevent page reloading
 */
function onSubmitFormAjax(oForm, oUserOptions) {
  var oOptions = {
    method : oForm.method,
    check : checkForm
  };
  
  Object.extend(oOptions, oUserOptions);
  
  // Check the form
  if (!oOptions.check(oForm)) {
    return false;
  }

	// Build url
  var url = new Url;
  var iElement = 0;
  var oElement = null;
  while (oElement = oForm.elements[iElement++]) {
    if ((oElement.type != "radio" && oElement.type != "checkbox") || oElement.checked) {
      url.addParam(oElement.name, oElement.value);
    }
  }

	// Launch
  url.requestUpdate(SystemMessage.id, oOptions);
  
  // return
  return false;
}


function submitFormAjaxOffline(oForm, ioTarget, oOptions) {
  if (oForm.attributes.onsubmit) {
    if (oForm.attributes.onsubmit.nodeValue) {        // this second test is only for IE
      if (!oForm.onsubmit()) {
        return;
      }
    }  
  }
  
  var url = new Url;
  var iElement = 0;
  var oElement = null;
  while (oElement = oForm.elements[iElement++]) {
    if ((oElement.type != "radio" && oElement.type != "checkbox") || oElement.checked) {
      url.addParam(oElement.name, oElement.value);
    }
  }

  var oDefaultOptions = {
    method : "post"
  };
  Object.extend(oDefaultOptions, oOptions);

  url.requestUpdateOffline(ioTarget, oDefaultOptions);
}

function setSelectionRange(textarea, selectionStart, selectionEnd) {
  if (textarea.setSelectionRange) {
    textarea.focus();
    textarea.setSelectionRange(selectionStart, selectionEnd);
  }
  else if (textarea.createTextRange) {
    var range = textarea.createTextRange();
    textarea.collapse(true);
    textarea.moveEnd('character', selectionEnd);
    textarea.moveStart('character', selectionStart);
    textarea.select();
  }
}

function setCaretToPos (textarea, pos) {
  setSelectionRange(textarea, pos, pos);
}

function insertAt(textarea, str) {
  // Inserts given text at selection or cursor position

  if (textarea.setSelectionRange) {
    // Mozilla UserAgent Gecko-1.4
    var scrollTop = textarea.scrollTop;

    var selStart = textarea.selectionStart;
    var selEnd   = textarea.selectionEnd  ;
		
    var strBefore = textarea.value.substring(0, selStart);
    var strAfter  = textarea.value.substring(selEnd);

    textarea.value = strBefore + str + strAfter;
		
    var selNewEnd = selStart + str.length;
    if (selStart == selEnd) { 
      // No selection: move caret
      setCaretToPos(textarea, selNewEnd);
    } else  {
      // Selection: re-select insertion
      setSelectionRange(textarea, selStart, selNewEnd);
    }
		
    textarea.scrollTop = scrollTop;
  } else if (document.selection) {
    // UserAgent IE-6.0
    textarea.focus();
    var range = document.selection.createRange();
    if (range.parentElement() == textarea) {
      var hadSel = range.text.length > 0;
      range.text = str;
      if (hadSel)  {
        range.moveStart('character', -range.text.length);
        range.select();
      }
    }
  } else { 
    // UserAgent Gecko-1.0.1 (NN7.0)
    textarea.value += str;
  }
}

function followUp(event) {
	// IE won't have a event target if handler is defined as an HTML attribute
	if (!event.target) {
		return;
	}
	
	// Redirect to next field
  var field = event.target;
  if (field.value.length == field.maxLength) {
    $(field.next()).activate();
  }
  
  return true;
}

Object.extend(Form, {
  toObject: function (oForm) {
    var aFieldsForm  = Form.getElements(oForm);
    var oDataForm = {};
    //  Récupération des données du formualaire
    aFieldsForm.each(function (value) {
      var sNameElement  = value["name"];
      var sValueElement = Form.Element.getValue(value);
      oDataForm[sNameElement] = sValueElement;
      }
    );
    return oDataForm;
  },
  fromObject: function(oForm, oObject){
    $H(oObject).each(function (pair) {
      var oField = oForm[pair.key];
      if(oField){
        oField.value = pair.value;
      }
    });
  }
} );


function NumericField (form, element, step, min, max, showPlus) {
    this.sField = form + "_" + element;
    this.min  = (min  != undefined) ? min  : null;
    this.max  = (max  != undefined) ? max  : null;
    this.step = (step != undefined) ? step : null;
    this.showPlus = showPlus | null;
}

NumericField.prototype = {
  // Increment function
  inc: function () {
    var oField = $(this.sField);
    var step = Number(this.getStep());
    var result = (parseInt(Number(oField.value) / step) + 1) * step;
    if (this.max != null) {
      result = (result <= this.max) ? result : this.max;
    }
    $V(oField, (((this.showPlus && result >= 0)?'+':'')+result), true);
    oField.select();
  },

  // Decrement function
  dec: function () {
    var oField = $(this.sField);
    var step = Number(this.getStep(-1));
    var result = (parseInt(Number(oField.value) / step) - 1) * step;
    if (this.min != null) {
 	    result = (result >= this.min) ? result : this.min;
    }
    $V(oField, (((this.showPlus && result >= 0)?'+':'')+result), true);
    oField.select();
  },
  
  /** Calculate appropriate step
   *  ref is the reference to calculate the step, it is useful to avoid having bad steps :
   *  for exampele, when we have oField.value = 10, if we decrement, we'll have 5 instead of 9 without this ref
   *  Set it to -1 when decrementing, 0 when incrementing
   */
  getStep: function (ref) {
    var oField = $(this.sField);
    if (this.step == null) {
      var value = Math.abs(oField.value) + ((ref != undefined) ? ref : 0);
      if (value < 10)  return 1;
      if (value < 50)  return 5;
      if (value < 100) return 10;
      if (value < 500) return 50;
      if (value < 1000) return 100;
      if (value < 5000) return 500;
      return 1000;
    } else {
      return this.step;
    }
  }
}

// The time picker
var TimePicker = Class.create({
  initialize: function(form, field) {
    var element = this;
    this.form = form;
    this.field = field;
    this.existing = false;

    // Form field
    prepareForm(this.form);
    var formField = $(document.forms[form].elements[field]);
    if (formField) {
      this.fieldId = formField.id;
      if (!formField.size) {
        formField.writeAttribute('size', 3).writeAttribute('maxlength', 5);
      }
      this.pickerId = this.fieldId+'_picker';
    } else return;
    
    this.closeEvent = this.closePicker.bindAsEventListener(this);
    
    // Get the data from the form field
    var parts = $V(formField).split(':');
    this.hour   = parts[0];
    this.minute = parts[1];
    
    // Time picker trigger
    this.trigger = $(this.fieldId+'_trigger');
    if (!this.trigger) {
      this.trigger = new Element('img')
                        .writeAttribute('src', 'images/icons/time.png');
      this.trigger.id = this.fieldId+'_trigger';
      formField.insert({after: this.trigger});
    }
    
    // Time hour-minute selector
    var picker = $(this.pickerId);
    if (!picker) {
      //picker.remove();
      picker = new Element('table')
                  .writeAttribute('id', this.pickerId)
                  .addClassName('time-picker');
      $('main').appendChild(picker);
      picker.absolutize().hide();
      this.existing = false;
    } else {
      this.existing = true;
    }
    
    if (!this.existing) {
        // Hours
      var str = '<tr><td><table class="hour"><tr>';
      for (i = 0; i < 24; i++) {
        if (i%12 == 0) str += '</tr><tr>';
        var h = printf('%02d', i);
        str += '<td class="hour-'+h+'">'+h+'</td>';
      }
      str += '</tr></table></td></tr>';
      
        // Minutes
      str += '<tbody class="long" style="display: none;"><tr><td><table class="minute"><tr>';
      for (i = 0; i < 60; i++) {
        if (i%10 == 0) str += '</tr><tr>';
        var m = printf('%02d', i);
        str += '<td class="minute-'+m+'">:'+m+'</td>';
      }
      str += '</tr></table></td></tr></tbody>';
      
        // Short minutes
      str += '<tbody class="short"><tr><td><table class="minute"><tr>';
      for (i = 0; i < 60; i=i+5) {
        if (i%30 == 0) str += '</tr><tr>';
        var m = printf('%02d', i);
        str += '<td class="minute-'+m+'">:'+m+'</td>';
      }
      str += '</tr></table></td></tr></tbody>';
  
      // Long-short switcher
      str += '<tr><td><div class="switch">&gt;&gt;</div></td></tr>';
      picker.insert(str);
  
      // Behaviour
        // on click on the switch "long-short"
      picker.select('.switch')[0].observe('click', element.toggleShortLong.bindAsEventListener(element));
      
        // on click on the hours 
      picker.select('.hour td').each(function(hour) {
        hour.observe('click', element.setHour.bindAsEventListener(element));
      });
      
        // on click on the minutes
      picker.select('.minute td').each(function(minute) {
        minute.observe('click', element.setMinute.bindAsEventListener(element));
      });
      
      this.highlight();
    }
    
    // on click on the trigger
    this.trigger.observe('click', element.togglePicker.bindAsEventListener(element));
    
    // on change
    formField.observe('change', element.getData.bindAsEventListener(element));
  },

  closePicker: function (e) {
    e = e.element();
    var picker = $(this.pickerId);
    var trigger = $(this.fieldId+'_trigger');
    if (!e.descendantOf(picker) && (e != trigger)) {
      picker.hide();
      document.body.stopObserving('mouseup', this.closeEvent);
    }
  },
  
  // Show the selector
  togglePicker: function() {
    var element = this;
    var picker = $(this.pickerId);
    
    // We hide every other picker
    $$('table.time-picker').each(function(o){if (o.id != element.pickerId) o.hide();});
    
    // The trigger position
    this.position = this.trigger.cumulativeOffset();
    this.position.top += this.trigger.getDimensions().height;

    // The picker position
    picker.toggle().setStyle({
      left: this.position.left+'px',
      top: this.position.top+'px'
    }).unoverflow();
    
    document.body.observe('mouseup', this.closeEvent);
  },
  
  // Set the hour
  setHour: function (e) {
    var picker = $(this.pickerId);
    this.hour = e.element().innerHTML;
    this.highlight();
    
    $V(this.fieldId, this.hour+':'+(this.minute?this.minute:'00'), true);
  },
  
  // Set the minutes
  setMinute: function (e) {
    var picker = $(this.pickerId);
    var field = $(this.fieldId);
    
    this.minute = e.element().innerHTML.substring(1,3);
    this.highlight();
    
    if (this.hour) {
      $V(field, this.hour+':'+this.minute, true);
      this.togglePicker();
    } else {
      $V(field, '00:'+this.minute, true);
    }
  },
  
  getData: function() {
    var field = $(this.fieldId);
    var data = $V(field).split(':');
    this.hour = data[0];
    this.minute = data[1];
    this.highlight();
  },
  
  highlight: function() {
    var picker = $(this.pickerId);
    
    var selected = picker.select('.hour td.selected');
    if (selected.length) {
      selected[0].removeClassName('selected');
    }
    if (this.hour && (selected = picker.select('.hour td.hour-'+this.hour))) {
      selected[0].addClassName('selected');
    }
  
    selected = picker.select('.minute td.selected');
    if (selected.length) {
      selected.each(function(o){o.removeClassName('selected');});
    }
    if (this.minute && (selected = picker.select('.minute td.minute-'+this.minute))) {
      selected.each(function(o){o.addClassName('selected');});
    }
  },
  
  toggleShortLong: function (e) {
    var picker = $(this.pickerId);
    var short = picker.select('.short')[0].toggle();
    picker.select('.long')[0].toggle();
    e.element().update(short.visible()?'&gt;&gt;':'&lt;&lt;');
  }
});

// Make a select form control with optgroups appear as a tree
Element.addMethods(['select', 'optgroup'], {
  buildTree: function (element, tree) {
    var select = element;
    if (!tree) { // If it is the root
      // New unordered list
      tree = new Element('ul').addClassName('select-tree');
      var pos = element.cumulativeOffset();
      var dim = element.getDimensions();
      
      // Every option of the select is hidden
      element.descendants().each(function(d) {d.hide()});
      element.setStyle({'width': dim.width+'px'});
      element.writeAttribute('size', 1);
      tree.setStyle({'width': dim.width+'px'});
      
      // The hack input to blur the select control
      var hack = new Element('input').writeAttribute('type', 'text').hide();
      hack.name = element.name+'_tree__hack';
      hack.id = element.id+'_tree__hack';
      element.insert({after: hack});
      
      // Insertion of the tree and click event
      tree.id = element.id+'_tree';
      element.insert({after: tree});
      
      // Toggle tree function
      toggleTree = function (e) {
        hack.focus();
        $$('ul.select-tree ul').each(function(ul) {ul.hide()});
        tree.toggle();
        document.body.observe('mouseup', close);
      };
      
      // Close function used to hide the tree on click on the body
      close = function (e) {
        e = e.element();
        if (!e.descendantOf(tree) && (e != element)) {
          document.body.stopObserving('mouseup', close);
          tree.hide();
        }
      };
      
      element.observe('click', toggleTree.bindAsEventListener(element));
      
      // Tree styling
      tree.setStyle({
        zIndex: 40,
        position: 'absolute',
        left: pos.left+parseInt(select.getStyle('margin-left').split('px')[0])+'px',
        top: pos.top+parseInt(select.getStyle('margin-top').split('px')[0])-1+dim.height+'px'
      }).hide();
    } else {
      // we retrieve the select
      while (select && select.tagName.toLowerCase() != 'select') {
         select = select.up();
      }
    }
    
    // For each select child
    element.childElements().each(function (o) {
      var li = new Element('li');
      
      // If it is an optgroup
      if (o.tagName.toLowerCase() == 'optgroup') {
        li.insert(o.label);
        
        // New sublist
        var subTree = new Element('ul');
        o.buildTree(subTree.hide());
        li.insert(subTree).addClassName('drop');
        
        // On mouse over on the LI
        li.observe('mouseover', function() {
          var liDim = li.getDimensions();
          var liPos = li.positionedOffset();
          
          // Every select-tree list is hidden
          $$('ul.select-tree ul').each(function(ul) {ul.hide()});
          
          // Every child element is drawn
          li.childElements().each(function (e) {
            e.show().setStyle({
              position: 'absolute',
              width: select.getWidth()+'px',
              left: liPos.left+liDim.width-1+'px',
              top: liPos.top+1+'px'
            });
          });
        });

      // If it is an option
      } else {
        li.insert(o.text);
        li.id = select.id+'_'+o.value;
        if ($V(select) == o.value) li.addClassName('selected');
        
        // on click on the li
        li.observe('click', function() {
          // we set the value and hide every other select tree
          $(select.id+'_'+$V(select)).removeClassName('selected');
          $V(select, o.value, true);
          li.addClassName('selected');
          $$('ul.select-tree').each(function(ul) {ul.hide()});
        });
        
        // we hide every other other select tree ul on mouseover
        li.observe('mouseover', function() {
          tree.select('ul').each(function(ul) {ul.hide()});
        });
      }
      tree.insert(li);
    });
    
    return element;
  }
});
