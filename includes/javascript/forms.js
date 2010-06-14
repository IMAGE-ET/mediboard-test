/* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

function confirmDeletion(oForm, oOptions, oOptionsAjax) {
  oOptions = Object.extend({
    typeName: "",
    objName : "",
    msg     : "Voulez-vous réellement supprimer ",
    ajax    : false,
    target  : "systemMsg",
    callback: null
  }, oOptions);
  
  if (oOptions.objName) oOptions.objName = " '" + oOptions.objName + "'";
  if (confirm(oOptions.msg + oOptions.typeName + " " + oOptions.objName + " ?" )) {
    oForm.del.value = 1;
    
    if (oOptions.callback) {
      oOptions.callback();
    }
    else {
      if(oOptions.ajax)
        submitFormAjax(oForm, oOptions.target, oOptionsAjax);
      else
        oForm.submit();
    }
  }
}

/** Universal get/set function for form elements
  * @param element A form element (Form.Element or id) : input, textarea, select, group of radio buttons, group of checkboxes
  * @param value   If set, sets the value to the element. Can be an array of values : ['elementvalue1', 'elementvalue2', ...] 
  * @param fire    Determines wether the onchange callback has to be called or not
  * @return        An array of values for multiple selectable elements, a boolean for 
  *                single checkboxes/radios, a string for textareas and text inputs
  */
function $V (element, value, fire) {
  if (!(element = $(element))) return;
  
  //element = element.name ? $(element.form[element.name]) : $(element);
  fire = Object.isUndefined(fire) ? true : fire;
  
  // We get the tag and the type
  var tag  = element.tagName || '',
      type = element.type || '',
      isInput = tag.match(/^(input|select|textarea)$/i),
      isElement = Object.isElement(element);

  if (isElement && !isInput) {
    return;
  }

  // If it is a form element
  if (isInput && isElement) {
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
        ret = (ret.length > 1 ? ret : ret[0]);
      }
      return (ret && ret.length > 0) ? ret : null;
    }
  }
  return;
}

function notNullOK(oEvent) {
  var oElement = oEvent.element ? oEvent.element() : oEvent,
      oLabel = Element.getLabel(oElement);
      
  if (oLabel) {
    oLabel.className = ($V(oElement.form[oElement.name]) ? "notNullOK" : "notNull");
  }
}

function canNullOK(oEvent) {
  var oElement = oEvent.element ? oEvent.element() : oEvent,
      oLabel = Element.getLabel(oElement);
      
  if (oLabel) {
    oLabel.className = ($V(oElement.form[oElement.name]) ? "notNullOK" : "canNull");
  }
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
};

function isKeyAllowed(event, allowed){
  var key = Event.key(event);
  if (!(key >= 48 && key <= 90 || 
        key >= 96 && key <= 111 || 
        key >= 186 && key <= 222)) return true;
  
  var c = String.fromCharCode(key);
  if (allowed.test(c)) return c;
}

function prepareForm(oForm) {
  var sFormName;
  
  if (typeof oForm == "string") {
    sFormName = oForm;
    oForm = document.forms[oForm];
  }
  
  if (!Object.isElement(oForm)) {
    try {
      console.warn((sFormName || oForm.name)+" is not an element or is a node list (forms with the same name ?)");
    } catch(e) {}
    return;
  }
  
  oForm = $(oForm);
  if (!oForm || oForm.hasClassName("prepared")) return;
  
  // Event Observer
  if(oForm.hasClassName("watched")) {
    new Form.Observer(oForm, 1, function() { FormObserver.elementChanged(); });
  }
  
  // Autofill of the form disabled (useful for the login form for example)
  oForm.setAttribute("autocomplete", "off");
  
  // Form preparation
  if (Prototype.Browser.IE && oForm.name && oForm.name.nodeName) // Stupid IE hack, because it considers an input named "name" as an attribute
    sFormName = oForm.cloneNode(false).getAttribute("name");
  else
    sFormName = oForm.getAttribute("name");

  oForm.lockAllFields = (oForm._locked && oForm._locked.value) == "1"; 

  // Build label targets
  var aLabels = oForm.select("label"),
      oLabel, sFor, i = 0;
      
  while (oLabel = aLabels[i++]) {
    if ((sFor = oLabel.htmlFor) && (sFor.indexOf(sFormName) !== 0)) {
      oLabel.htmlFor = sFormName + "_" + sFor;
    }
  }

  // XOR modifications
  var xorFields, re = /xor(?:\|(\S+))+/g;
  while (xorFields = re.exec(oForm.className)) {
    xorFields = xorFields[1].split("|");
    
    xorFields.each(function(xorField){
      var element = $(oForm.elements[xorField]);
      if (!element) return;
      
      element.xorElementNames = xorFields.without(xorField);
      
      var checkXOR = (function(){
        if ($V(this)) {
          this.xorElementNames.each(function(e){
            $V(this.form.elements[e], '');
          }, this);
        }
      }).bindAsEventListener(element);
      
      element.observe("change", checkXOR)
             .observe("keyup", checkXOR)
             .observe("ui:change", checkXOR);
    });
  }

  // For each element
  var i = 0, oElement;
  while (oElement = $(oForm.elements[i++])) {
    var sElementName = oElement.getAttribute("name"),
        props = oElement.getProperties();

    // Locked object
    if (oForm.lockAllFields) {
      oElement.disabled = true;
    }
    
    // Create id for each element if id is null
    if (!oElement.id && sElementName) {
      oElement.id = sFormName + "_" + sElementName;
      if (oElement.type === "radio") {
        oElement.id += "_" + oElement.value;
      }
    }

    // If the element has a mask and other properties, they may conflict
    if (Preferences.INFOSYSTEM && props.mask) {
      Assert.that(!(
        props.min || props.max || props.bool || props.ref || props.pct || props.num
      ), "'"+oElement.id+"' mask may conflit with other props");
    }
    
    // Can null
    if (props.canNull) {
      oElement.observe("change", canNullOK)
              .observe("keyup",  canNullOK)
              .observe("ui:change", canNullOK);
    }

    // Not null
    if (props.notNull) {
      oElement.observe("change", notNullOK)
              .observe("keyup",  notNullOK)
              .observe("ui:change", notNullOK);
    }
    else {
      var oLabel = Element.getLabel(oElement);
      if (oLabel) {
        oLabel.removeClassName("checkNull");
      }
    }

    // ui:change is a custom event fired on the native onchange throwed by $V, 
    // because fire doesn't work with native events 
    // Fire it only if the element has a spec
    if (oElement.className) {
      oElement.fire("ui:change");
    }

    // Select tree
    if (props["select-tree"] && Prototype.Browser.Gecko) {
      oElement.buildTree();
    }

    var mask = props.mask;
    if (mask) {
      mask = mask.gsub('S', ' ').gsub('P', '|');
      oElement.mask(mask);
    }
    
    // Default autocomplete deactivation
    if (oElement.type === "text") {
      oElement.writeAttribute("autocomplete", "off");
    }
    
    // Won't make it resizable on IE
    if (oElement.type === "textarea" && 
        oElement.id !== "htmlarea") {
      oElement.setResizable({autoSave: true, step: 'font-size'});
    }
    
    // Focus on first text input
    if (bGiveFormFocus && oElement.clientWidth > 0 && 
        !oElement.getAttribute("disabled") && !oElement.getAttribute("readonly") && 
        oElement.type === "text") {
      
      oElement.writeAttribute("autofocus", "autofocus");
      
      var i, applets = document.applets;
      
      if (applets.length) {
        window._focusElement = oElement;
        
        var inactiveApplets;
        var tries = 50;
            
        function waitForApplet() {
          inactiveApplets = applets.length;
          
          for(i = 0; i < applets.length; i++) {
            if (Prototype.Browser.IE || applets[i].isActive && applets[i].isActive()) inactiveApplets--;
            else break;
          }
          if (inactiveApplets == 0) {
            window._focusElement.focus();
            return;
          }
          else if (tries--) setTimeout(waitForApplet, 100);
        }

        waitForApplet();
      }
      else oElement.focus();
      bGiveFormFocus = false;
    }
    		
    // We mark this form as prepared
    oForm.addClassName("prepared");
  }
}

function prepareForms(root) {
  root = $(root || document.documentElement);
  
  try {
    root.select("form:not(.prepared)").each(prepareForm);
    
    root.select("button.singleclick").each(function(button) {
      button.observe('click', function(event) {
        var element = Event.element(event);
        Form.Element.disable(element);
        Form.Element.enable.delay(1, element);
      });
    });
    
    // We set a title on the button if it is a .notext and if it hasn't one yet
    root.select("button.notext:not([title])").each(function(button) {
      button.title = (button.textContent || button.innerHTML).strip();
    });
  } catch (e) {}
}

function submitFormAjax(oForm, ioTarget, oOptions) {
  // the second test is only for IE
  if (oForm.attributes.onsubmit &&
      oForm.attributes.onsubmit.nodeValue &&
      !oForm.onsubmit()) return;

  var url = new Url, i = 0, oElement;
  while (oElement = oForm.elements[i++]) {
    if (!oElement.disabled && ((oElement.type != "radio" && oElement.type != "checkbox") || oElement.checked)) {
      url.addParam(oElement.name, oElement.value);
    }
  }

  oOptions = Object.extend({
    method : oForm.method
  }, oOptions);

  url.requestUpdate(ioTarget, oOptions);
}

/**
 * Submit a form in Ajax mode
 * New version to plage in onsubmit event of the form
 * @param oForm Form element
 * @return false to prevent page reloading
 */
function onSubmitFormAjax(oForm, oOptions) {
  oOptions = Object.extend({
    method : oForm.method,
    check  : checkForm
  }, oOptions);
  
  // Check the form
  if (!oOptions.check(oForm)) {
    return false;
  }

  // Build url
  var url = new Url, i = 0, oElement;
  while (oElement = oForm.elements[i++]) {
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
  // the second test is only for IE
  if (oForm.attributes.onsubmit &&
      oForm.attributes.onsubmit.nodeValue &&
      !oForm.onsubmit()) return;
  
  var url = new Url, i = 0, oElement;
  while (oElement = oForm.elements[i++]) {
    if ((oElement.type != "radio" && oElement.type != "checkbox") || oElement.checked) {
      url.addParam(oElement.name, oElement.value);
    }
  }

  oOptions = Object.extend({
    method : "post"
  }, oOptions);

  url.requestUpdateOffline(ioTarget, oOptions);
}

Object.extend(Form, {
  toObject: function (form) {
    var fields = form.elements,
        object = {};
    
    //  Récupération des données du formualaire
    fields.each(function (field) {
      object[field.name] = $V(field);
    });
    return object;
  },
  fromObject: function(form, object){
    $H(object).each(function (pair) {
      $V(form.elements[pair.key], pair.value);
    });
  }
});

// Form getter
function getForm (form, prepare) {
  if (Object.isString(form))
    form = $(document.forms[form]);
  
  if (Object.isUndefined(prepare))
    prepare = true;
  
  if (prepare) prepareForm(form);
  return form;
}

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
