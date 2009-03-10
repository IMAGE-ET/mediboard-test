/* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

function addHelp(sClass, oField, sName, sDepend1, sDepend2) {
  url = new Url;
  if(!sDepend1) {
    sDepend1 = null;
  }
  if(!sDepend2) {
    sDepend2 = null;
  }
  url.setModuleAction("dPcompteRendu", "edit_aide");
  url.addParam("class"       , sClass);
  url.addParam("field"       , sName || oField.name);
  url.addParam("text"        , oField.value);
  url.addParam("depend_value_1", sDepend1);
  url.addParam("depend_value_2", sDepend2)
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
  if (!oElement.form) return null;
  
  var aLabels = $(oElement.form).select("label");
  var iLabel = 0;
  while (oLabel = aLabels[iLabel++]) {
    if (oElement.id == oLabel.htmlFor) {
      return oLabel;
    }
  } 
  
  return null; 
}

Element.addMethods({
  getLabel: function (element) {
    return getLabelFor(element);
  }
});

/** Universal get/set function for form elements
  * @param element A form element (Form.Element or id) : input, textarea, select, group of radio buttons, group of checkboxes
  * @param value   If set, sets the value to the element. Can be an array of values : ['elementvalue1', 'elementvalue2', ...] 
  * @param fire    Determines wether the onchange callback has to be called or not
  * @return        An array of values for multiple selectable elements, a boolean for 
  *                single checkboxes/radios, a string for textareas and text inputs
  */
function $V (element, value, fire) {
  if (!element) {
    Console.trace("Warning: This form field doesn't exist");
    return;
  }
  
  //element = element.name ? $(element.form[element.name]) : $(element);
  element = $(element);
  fire = Object.isUndefined(fire) ? true : fire;
  
  // We get the tag and the type
  var tag  = element.tagName || '';
  var type = element.type    || '';

  // If it is a form element
  if (Object.isElement(element) && tag.match(/^(input|select|textarea)$/i)) {
    // If the element is a checkbox, we check if it's checked
    var oldValue = (type.match(/^checkbox$/i) ? element.checked : $F(element));

    // If a value is provided
    if (!Object.isUndefined(value) && value != oldValue) {
      element.setValue(value);
      if (fire) {
        (element.onchange || Prototype.emptyFunction).bindAsEventListener(element)();
        element.fire("ui:change");
      }
    }
    
    // else, of no value is provided
    else {
      return oldValue;
    }
  } 
  
  // If the element is a list of elements (like radio buttons)
  else if (Object.isArray(element) || (element[0] && Object.isElement(element[0]))) {
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
          ret[ret.length] = e.value;
        }
        type = e.type;
      });
      
      if (type.match(/^radio$/i)) {
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
  var oAreaField = $(oForm.elements[sPropName]);

  var sValue = oHelpElement.value;
  oHelpElement.value = "";
  var caret = oAreaField.caret();
  oAreaField.caret(caret.begin, caret.end, sValue + '\n');
  oAreaField.caret(oAreaField.value.length);
  oAreaField.scrollTop = oAreaField.scrollHeight;
}

function putHelperContent(oElem, sFieldSelect) {
  var oForm      = oElem.form;
  var sDependsOn = $V(oElem);

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

function notNullOK(oEvent) {
  var oElement = oEvent.element();
  if (oLabel = oElement.getLabel()) {
    oLabel.className = ($V(oElement) ? "notNullOK" : "notNull");
  }
}

function canNullOK(oEvent) {
  var oElement = oEvent.element();
  if (oLabel = oElement.getLabel()) {
    oLabel.className = ($V(oElement) ? "notNullOK" : "canNull");
  } 
}

function getSurroundingForm(element) {
  var parent = element.up();
  while (parent && !parent.nodeName.match(/^form$/i)) {
    parent = parent.parentNode;
  }
  return parent;
}

var bGiveFormFocus = true;

var FormObserver = {
  changes       : 0,
  lastFCKChange : 0,
  fckEditor     : null,
  checkChanges  : function() {
    return !this.changes;
  },
  elementChanged : function() {
    this.changes++;
  },
  FCKChanged : function(timer) {
    if(this.lastFCKChange < timer) {
      this.elementChanged();
    }
    this.lastFCKChange = timer;
  }
}

Element.addMethods({
  setResizable: function (element, options) {
    options = Object.extend({
      autoSave: true,
      step: 1
    }, options);
  
    var staticOffset = null;
    var cookie = new CookieJar(); 
    
    // oGrippie is the draggable element
    var oGrippie = new Element('div');
    
    // We remove the margin between the textarea and the grippie
    element.style.marginBottom = '0';
    
    // grippie's class and style
    oGrippie.addClassName('grippie-h');
    oGrippie.setOpacity(0.5);
    if (!element.visible()) {
      oGrippie.hide();
    }
    
    // When the mouse is pressed on the grippie, we begin the drag
    oGrippie.onmousedown = startDrag;
    element.insert({after: oGrippie});
    
    // Loads the height maybe saved in a cookie
    function loadHeight() {
      if (h = cookie.getValue('ElementHeight', element.id)) {
        element.setStyle({height: (h+'px')});
      }
    }
    loadHeight.defer(); // deferred to prevent Firefox 2 resize bug
    
    function startDrag(e) {
      staticOffset = element.getHeight() - e.pointerY(); 
      element.setOpacity(0.4);
      document.onmousemove = performDrag;
      document.onmouseup = endDrag;
      return false;
    }
  
    function performDrag(e) {
      var h = null;
      if (typeof options.step == 'string') {
        var iStep = element.getStyle(options.step);
        iStep = iStep.substr(0, iStep.length - 2);
        
        h = Math.max(iStep*2, staticOffset + e.pointerY()) - Math.round(oGrippie.getHeight()/2);
        h = Math.round(h / iStep)*iStep;
      } else {
        h = Math.max(32, staticOffset + e.pointerY());
      }
      element.setStyle({height: h + 'px'});
      return false;
    }
  
    function endDrag(e) {
      element.setStyle({opacity: 1});
      document.onmousemove = null;
      document.onmouseup = null;

      if (element.id) {
        cookie.setValue('ElementHeight', element.id, element.getHeight() - Math.round(oGrippie.getHeight()/2));
      }
      return false;
    }
  }
} );

function prepareForm(oForm, bForcePrepare) {
  if (typeof oForm == "string") {
    oForm = document.forms[oForm];
  }
  oForm = $(oForm);
  
  if (oForm) {
		// If this form hasn't been prepared yet
		if (!oForm.hasClassName("prepared") || bForcePrepare) {
		
		  // Event Observer
		  if(oForm.hasClassName("watched")) {
		    new Form.Observer(oForm, 1, function() { FormObserver.elementChanged(); });
		  }
		  
		// Form preparation
		  
		  if (Prototype.Browser.IE) // Stupid IE hack, because it considers an input named "name" as an attribute
		  	var sFormName = oForm.cloneNode(false).getAttribute("name");
		  else
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
		  	
		  	var elementClassNames = $w(oElement.className);
		  	var sElementName = oElement.getAttribute("name");
		  	var props = oElement.getProperties();
		
		  	// Locked object
		  	if (oForm.lockAllFields) {
		  		oElement.disabled = true;
		  	}
		  	
		    // Create id for each element if id is null
		    if (!oElement.id && sElementName) {
		      oElement.id = sFormName + "_" + sElementName;
		      if (oElement.type == "radio") {
		        oElement.id += "_" + oElement.value;
		      }
		    }
		
		    // If the element has a mask and other properties, they may conflict
		    if (Preferences.INFOSYSTEM && props.mask) {
		      Assert.that(!(
		        props.min || props.max || props.minMax || props.bool || props.ref || props.pct || props.num
		      ), "'"+oElement.id+"' mask may conflit with other props");
		    }

			  // Not null
    	  if (elementClassNames.indexOf("notNull") != -1) {
          oElement.observe("change", notNullOK)
                  .observe("keyup",  notNullOK)
                  .observe("ui:change", notNullOK);
		    }

        // Can null
        if (elementClassNames.indexOf("canNull") != -1) {
          oElement.observe("change", canNullOK)
                  .observe("keyup",  canNullOK)
                  .observe("ui:change", canNullOK);
        }
        
        // ui:change is a custom event fired on the native onchange throwed by $V, 
        // because fire doesn't work with native events 
        oElement.fire("ui:change");

		    // Select tree
		    if (elementClassNames.indexOf("select-tree") != -1 && Prototype.Browser.Gecko) {
		      oElement.buildTree();
		    }
		
		    if (mask = props.mask) {
		      mask = mask.gsub('S', ' ').gsub('P', '|');
		      oElement.mask(mask);
		    }
		    
		    // Default autocomplete deactivation
		    if (oElement.type == "text") {
		    	oElement.writeAttribute("autocomplete", "off");
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
		    if (oElement.type == "textarea" && oElement.id != "htmlarea" && (Prototype.Browser.Gecko || Prototype.Browser.Opera)) {
		      oElement.setResizable({autoSave: true, step: 'font-size'});
		    }
		    
		    // We mark this form as prepared
		    oForm.addClassName("prepared");
		  }
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
    if (!oElement.disabled && ((oElement.type != "radio" && oElement.type != "checkbox") || oElement.checked)) {
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
    if (!oElement.disabled && ((oElement.type != "radio" && oElement.type != "checkbox") || oElement.checked)) {
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

Object.extend(Form, {
  toObject: function (oForm) {
    var aFieldsForm  = Form.getElements(oForm);
    var oDataForm = {};
    //  Récupération des données du formualaire
    aFieldsForm.each(function (value) {
      var sNameElement  = value["name"];
      var sValueElement = $V(value);
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

Element.addMethods('input', {
  addSpinner: function(element, options) {
    options = Object.extend({
      min: null,
      max: null,
      step: null,
      decimals: null,
      showPlus: false,
      spinnerElement: null
    }, options);
    
    element.spinner = {
      /** Calculate appropriate step
       *  ref is the reference to calculate the step, it is useful to avoid having bad steps :
       *  for exampele, when we have oField.value = 10, if we decrement, we'll have 5 instead of 9 without this ref
       *  Set it to -1 when decrementing, 0 when incrementing
       */
      getStep: function (ref) {
        ref |= 0;
        if (options.step == null) {
          var value = Math.abs(element.value) + ref;
          if (value < 10)  return 1;
          if (value < 50)  return 5;
          if (value < 100) return 10;
          if (value < 500) return 50;
          if (value < 1000) return 100;
          if (value < 5000) return 500;
          return 1000;
        } else {
          return options.step;
        }
      },
     
      // Increment function
      inc: function () {
        var step = Number(element.spinner.getStep());
        var result = (parseInt(Number(element.value) / step) + 1) * step;
        if (options.max != null) {
          result = (result <= options.max) ? result : options.max;
        }
        if (options.decimals !== null) {
          result = printf("%."+options.decimals+"f", result);
        }
        result = ((options.showPlus && result >= 0)?'+':'')+result;
        
        $V(element, result, true);
        element.select();
      },
    
      // Decrement function
      dec: function () {
        var step = Number(element.spinner.getStep(-1));
        var result = (parseInt(Number(element.value) / step) - 1) * step;
        if (options.min != null) {
          result = (result >= options.min) ? result : options.min;
        }
        if (options.decimals !== null) {
          result = printf("%."+options.decimals+"f", result);
        }
        result = ((options.showPlus && result >= 0)?'+':'')+result;
        
        $V(element, result, true);
        element.select();
      }
    }
    
    Main.add(function () {
      if (!options.spinnerElement) {
        var container = new Element('div', {className: 'control numericField'});
        element.wrap(container);
        element.addClassName('num');
        
        options.spinnerElement = new Element('img', {src: './images/icons/numeric_updown.gif', usemap: '#arrow_'+element.id, alt: 'spinner'});
        container.insert(options.spinnerElement);
  
        var map  = new Element('map', {name: 'arrow_'+element.id});
        container.insert(map);
        
        map.insert(new Element('area', {coords:'0,0,10,8',   href:'#1', tabIndex:10000, title:'+'}).observe('click', element.spinner.inc));
        map.insert(new Element('area', {coords:'0,10,10,18', href:'#1', tabIndex:10000, title:'-'}).observe('click', element.spinner.dec));
      }
    
      if (element.disabled && options.spinnerElement) {
        options.spinnerElement.hide();
      }
    });
  }
});

// The time picker
var TimePicker = Class.create({
  initialize: function(form, field) {
    var element = this;
    this.form = form;
    this.field = field;

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
      this.trigger = new Element('img', {src: 'images/icons/time.png', id: this.fieldId+'_trigger'});
      formField.insert({after: this.trigger});
    }
    if (formField.disabled) {
      this.trigger.hide();
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
  
  buildPicker: function () {
    var element = this;
		picker = new Element('table', {id: this.pickerId, className: 'time-picker'});
		$('main').appendChild(picker);
		picker.absolutize().hide();
		
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
  },
  
  // Show the selector
  togglePicker: function() {
    var element = this;
    var picker = $(this.pickerId);
    
    if (!picker) {
      this.buildPicker();
      picker = $(this.pickerId);
    }
    
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
    var data = $V($(this.fieldId)).split(':');
    this.hour = data[0];
    this.minute = data[1];
    this.highlight();
  },
  
  highlight: function() {
    var picker = $(this.pickerId);
    
    var selected = picker.select('.hour td.selected, .minute td.selected');
    if (selected.length) {
      selected.each(function(o){o.removeClassName('selected')});
    }
    
    if (this.hour && (selected = picker.select('.hour td.hour-'+this.hour))) {
      selected.each(function(o){o.addClassName('selected');});
    }
  
    if (this.minute && (selected = picker.select('.minute td.minute-'+this.minute))) {
      selected.each(function(o){o.addClassName('selected');});
    }
  },
  
  toggleShortLong: function (e) {
    var picker = $(this.pickerId);
    var shortView = picker.select('.short')[0].toggle();
    picker.select('.long')[0].toggle();
    e.element().update(shortView.visible()?'&gt;&gt;':'&lt;&lt;');
  }
});

Element.addMethods('select', {
  buildTree: function (element, options) {
    var select  = element; // DOM select
    var search  = null; // DOM text input
    var tree    = null; // DOM UL/LI tree representing the select/optgroup
    var list    = null; // DOM UL/LI list for keyword search
    var pos     = null; // DOM select position
    var dim     = null; // DOM select dimensions
    
    options = Object.extend({
      className: 'select-tree'
    }, options);
    
    // Utility functions ////////
    var hideSelectTrees = function () {
      $$('ul.'+options.className+' ul').each(function(ul) {ul.hide()});
    }
    
    var validKey = function (keycode) {
      return (keycode >= 48 && keycode <= 90 || // letters and digits
              keycode >= 96 && keycode <= 111 || // num pad
              keycode >= 186 && keycode <= 181 ||
              keycode >= 219 && keycode <= 222 ||
              keycode == 32 || // space
              keycode == 8); // backspace
    }
    
    var updateCoordinates = function () {
      pos = select.cumulativeOffset();
      dim = select.getDimensions();
      
      pos.left = pos.left+parseInt(select.getStyle('margin-left').split('px')[0])+'px';
      pos.top  = pos.top +parseInt(select.getStyle('margin-top').split('px')[0])-1+dim.height+'px';
    }
    
    var reposition = function () {
      updateCoordinates();
      var style = {zIndex: 40, position: 'absolute', left: pos.left, top: pos.top};
      tree.setStyle(style);
      list.setStyle(style);
    }
    
    var makeTree = function (sel, ul) {
      updateCoordinates();
      var style = {width: dim.width+'px'};
      select.setStyle(style).childElements().each(function(d) {d.hide()});
      tree.setStyle(style);
      list.setStyle(style);
      search.setStyle({width: dim.width-4+'px'});
      
      ul.update(null);
      
      sel.childElements().each(function (o) {
        var li = new Element('li').addClassName(o.className);
        li.setStyle({
          color: o.getStyle('color'),
          borderLeft: o.getStyle('border-left'),
          borderRight: o.getStyle('border-right'),
          borderTop: o.getStyle('border-top'),
          borderBottom: o.getStyle('border-bottom')
        });
        
        // If it is an optgroup
        if (o.tagName.toLowerCase() == 'optgroup') {
          li.insert(o.label?o.label:'&nbsp;');
          
          // New sublist
          var subTree = new Element('ul');
          makeTree(o, subTree.hide());
          li.insert(subTree).addClassName('drop');
          
          // On mouse over on the LI
          li.observe('mouseover', function() {
            var liDim = li.getDimensions();
            var liPos = li.positionedOffset();
            
            // Every select-tree list is hidden
            hideSelectTrees();
            
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
          li.insert(o.text?o.text:'&nbsp;');
          li.id = select.id+'_'+o.value;
          
          // on click on the li
          li.observe('click', function() {
            // we set the value and hide every other select tree
            $V(select, o.value, true);
            tree.highlight();
            $$('ul.'+options.className).each(function(ul) {ul.hide()});
          });
          
          // we hide every other other select tree ul on mouseover
          li.observe('mouseover', function() {
            tree.select('ul').each(function(ul) {ul.hide()});
          });
        }
        ul.insert(li);
      });
      tree.highlight();
    }
    /////////////////////////////
    
    // Every element is hidden, but preserves its width
    select.childElements().each(function(d) {
      d.setOpacity(0.01);
      d.setStyle({height: 0});
    });
    
    // Tree -------------
    tree = new Element('ul', {"class": options.className, id: select.id+'_tree'});
    tree.display = function (e) {
      if (tree.empty()) {
        makeTree(select, tree);
      }
      search.focus();
      hideSelectTrees();
      reposition();
      tree.show();
      
      document.body.observe('mouseup', tree.undisplay);
      return false;
    }
    
    tree.undisplay = function (e) {
      document.body.stopObserving('mouseup', tree.undisplay);
      tree.hide();
    }
    
    tree.highlight = function () {
      var selected = tree.select('.selected');
      var val = $V(select);
      selected.each(function(s) {
        s.removeClassName('selected');
      });
      if (val && (s = $(select.id+'_'+val))) {
        s.addClassName('selected');
      }
    }
    select.insert({after: tree.hide()});
    
    // List -------------
    list = new Element('ul')
              .addClassName(options.className);
    list.id = select.id+'_list';
    
    list.navigate = function (e) {
      if (search.value != '') {
        var keycode;
        if (window.event) keycode = window.event.keyCode;
        else if (e) keycode = e.which;
        
        var focused = list.select('.focused');
        
        switch (keycode) {
          case 37:
          case 38:
            if (focused && (focused = focused[0])) {
              focused.removeClassName('focused');
              if (!(focused = focused.previous())) {
                focused = list.childElements().last();
              }
              focused.addClassName('focused');
            } else if (!list.empty()) {
              list.childElements().last().addClassName('focused');
            }
          
          break;
          case 39: 
          case 40:
            if (focused && (focused = focused[0])) {
              focused.removeClassName('focused');
              if (!(focused = focused.next())) {
                focused = list.firstDescendant();
              }
              focused.addClassName('focused');
            } else if (!list.empty()) {
              list.firstDescendant().addClassName('focused');
            }
          
          break;
        }
      }
    }
    
    list.search = function(s) {
      var children = select.descendants();
      var li = null;
      list.update(null);
      if (s) {
        children.each(function (c) {
          if (c.tagName.toLowerCase() == 'option' && c.text.toLowerCase().include(s.toLowerCase())) {
            var re = new RegExp(s, "i");
            li = new Element('li').update(c.text.gsub(re, function(match){return '<span class="highlight">'+match+'</span>'}));
            li.onclick = function() {
              $V(select, c.value, true);
              tree.highlight();
              search.value = null;
              select.display(false);
            };
            list.insert(li);
          }
        });
      }
    }
    select.insert({after: list.hide()});
    
    reposition();

    // search ----------
    search = new Element('input', {type: 'text', autocomplete: 'off'})
                 .setStyle({
                   position: 'absolute',
                   top: '-1000px'
                 });
    search.name = select.name+'_tree__search';
    search.id   = select.id+'_tree__search';
    
    search.catchKey = function (e) {
      var keycode;
      if (window.event) keycode = window.event.keyCode;
      else if (e) keycode = e.which;

      if (validKey(keycode)) { // Valid keycode
        if (keycode == 8 && search.value == '' && !select.visible()) {
          select.display(true);
        } else {
          list.search(search.value);
        }
      }
      else if (keycode == 27) { // Escape
        select.display(false);
      } 
      else if (keycode == 13) { // Enter
        var focused = list.select('.focused');
        if (focused && (focused = focused[0])) {
          focused.onclick(e.stop());
        }
        search.value = null;
      }
    }
    
    search.display = function (e) {
      var keycode;
      if (window.event) keycode = window.event.keyCode;
      else if (e) keycode = e.which;
      
      if (validKey(keycode) && keycode != 8 && keycode != 27) {
        select.hide();
        tree.undisplay();
        list.update(null).show();
        search.setStyle({position: 'relative', top: 0})
              .stopObserving('keydown', search.display);
      }
    }
    select.insert({after: search});
    
    // The search input to blur the select control and catch keys
    search.observe('keydown', search.display)
          .observe('keydown', list.navigate)
          .observe('keydown', search.catchKey);

    // Select
    select.writeAttribute('size', 1);
    
    select.display = function (show) {
      search.setStyle({position: 'absolute', top: '-1200px'});
      select.show();
      list.hide();
      if (show) tree.display();
      search.value = null;
      search.observe('keydown', search.display);
    }

    select.onclick = tree.display;
  }
});

// Form getter
function getForm (form, prepare) {
  prepare = prepare || true;
  if (Object.isString(form) && document.forms[form]) {
    form = $(document.forms[form]);
  }
  if (prepare) prepareForm(form);
  return form;
};

// Return the list of the elements, taking in account that ther can be nodelists of fields (like radio buttons)
Element.addMethods('form', {
  getElementsEx: function (form) {
    var list = [];
    form.getElements().each(function (element) {
      if (!list.find(function (e) {return (!!e && element.name == e.name)}))
        list.push(form.elements[element.name]);
    });
    return list;
  }
});
