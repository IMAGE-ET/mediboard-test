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

Element.addMethods({
  getLabel: function (element) {
    /*if (!element.form) return null;
  
    var labels = $(element.form).select("label"),
        label, i = 0;
    while (label = labels[i++]) {
      if (element.id == label.htmlFor) {
        return label;
      }
    }
    return null; */
    
    if (!element || !element.form || !element.id) return;
    return $(element.form).down("label[for='"+element.id+"'], label[htmlFor='"+element.id+"']");
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
  if (!(element = $(element))) return;
  
  //element = element.name ? $(element.form[element.name]) : $(element);
  fire = Object.isUndefined(fire) ? true : fire;
  
  // We get the tag and the type
  var tag  = element.tagName || '',
      type = element.type || '';

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

function notNullOK(oEvent) {
  var oLabel, oElement = oEvent.element ? oEvent.element() : oEvent;
  if (oLabel = Element.getLabel(oElement)) {
    oLabel.className = ($V(oElement.form[oElement.name]) ? "notNullOK" : "notNull");
  }
}

function canNullOK(oEvent) {
  var oLabel, oElement = oEvent.element ? oEvent.element() : oEvent;
  if (oLabel = Element.getLabel(oElement)) {
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

Element.addMethods({
  setResizable: function (element, options) {
    options = Object.extend({
      autoSave: true,
      step: 1
    }, options);
  
    var staticOffset, 
        cookie = new CookieJar(),
        grippie = new Element('div'); // the draggable element
    
    // We remove the margin between the textarea and the grippie
    $(element).setStyle({
      marginBottom: 0,
      resize: 'none'
    });
    
    // grippie's class and style
    grippie.addClassName('grippie-h').setOpacity(0.5);
    if (!element.visible()) {
      grippie.hide();
    }
    
    // When the mouse is pressed on the grippie, we begin the drag
    grippie.observe('mousedown', startDrag);
    element.insert({after: grippie});
    
    // Loads the height maybe saved in a cookie
    function loadHeight() {
      var h = cookie.getValue('ElementHeight', element.id);
      if (h)
        element.setStyle({height: (h+'px')});
    }
    loadHeight.defer(); // deferred to prevent Firefox 2 resize bug
    
    function startDrag(e) {
      Event.stop(e);
      staticOffset = element.getHeight() - e.pointerY(); 
      element.setOpacity(0.4);
      document.observe('mousemove', performDrag)
              .observe('mouseup', endDrag);
    }
  
    function performDrag(e) {
      Event.stop(e);
      var h, iStep;
      if (typeof options.step == 'string') {
        iStep = element.getStyle(options.step);
        iStep = iStep.substr(0, iStep.length - 2);
        
        h = Math.max(iStep*2, staticOffset + e.pointerY()) - Math.round(grippie.getHeight()/2);
        h = Math.round(h / iStep)*iStep;
      } else {
        h = Math.max(32, staticOffset + e.pointerY());
      }
      element.setStyle({height: h + 'px'});
    }
  
    function endDrag(e) {
      Event.stop(e);
      element.setOpacity(1);
      document.stopObserving('mousemove', performDrag)
              .stopObserving('mouseup', endDrag);

      if (element.id) {
        cookie.setValue('ElementHeight', element.id, element.getHeight() - Math.round(grippie.getHeight()/2));
      }
    }
  }
});

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

    if (mask = props.mask) {
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
      
      var i, applets = document.applets;
      if (applets.length) {
        window._focusElement = oElement;
        var inactiveApplets = applets.length,
            tries = 50;
            
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
  try {
    $(root || document.documentElement).select("form:not(.prepared)").each(prepareForm);
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
  toObject: function (oForm) {
    var aFieldsForm  = Form.getElements(oForm);
    var oDataForm = {};
    //  Récupération des données du formualaire
    aFieldsForm.each(function (value) {
      oDataForm[value.name] = $V(value);
    });
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
      fraction: false
    }, options);
    
    element.spinner = {
      /** Calculate appropriate step
       *  ref is the reference to calculate the step, it is useful to avoid having bad steps :
       *  for exampele, when we have oField.value = 10, if we decrement, we'll have 5 instead of 9 without this ref
       *  Set it to -1 when decrementing, 0 when incrementing
       */
      getStep: function (ref) {
        ref = ref || 0;
        if (options.step == null) {
          var value = Math.abs(element.value) + ref;
          if (options.fraction && (value < 1))  return 0.25;
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
        if (element.disabled || element.readOnly) return;
        
        var step = Number(element.spinner.getStep(0.1));
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
        if (element.disabled || element.readOnly) return;
        
        var step = Number(element.spinner.getStep(-0.1));
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
    
    var table = '<table class="control numericField"><tr><td style="padding:0;border:none;" /><td class="arrows" style="padding:0;border:none;"><div class="up"></div><div class="down"></div></td></tr></table>';
    element.insert({before: table});
    table = element.previous();
    table.select('td')[0].update(element);

    var arrows = table.select('.arrows div');
    arrows[0].observe('click', element.spinner.inc);
    arrows[1].observe('click', element.spinner.dec);
  }
});

Element.addMethods('select', {
  buildTree: function (element, options) {
    var select = element, // DOM select
        search, // DOM text input
        tree,   // DOM UL/LI tree representing the select/optgroup
        list,   // DOM UL/LI list for keyword search
        pos,    // DOM select position
        dim;    // DOM select dimensions
    
    options = Object.extend({
      className: 'select-tree'
    }, options);
    
    // Utility functions ////////
    var hideSelectTrees = function () {
      $$('ul.'+options.className+' ul').invoke('hide');
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
      
      pos.left = pos.left+parseInt(select.getStyle('margin-left'))+'px';
      pos.top  = pos.top +parseInt(select.getStyle('margin-top'))-1+dim.height+'px';
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
      select.setStyle(style).childElements().invoke('hide');
      tree.setStyle(style);
      list.setStyle(style);
      search.setStyle({width: dim.width-4+'px'});
      
      ul.update();
      
      sel.childElements().each(function (o) {
        var li = new Element('li').addClassName(o.className);
        /*li.setStyle({
          color: o.getStyle('color'),
          borderLeft: o.getStyle('border-left'),
          borderRight: o.getStyle('border-right'),
          borderTop: o.getStyle('border-top'),
          borderBottom: o.getStyle('border-bottom')
        });*/
        
        // If it is an optgroup
        if (/optgroup/i.test(o.tagName)) {
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
            $$('ul.'+options.className).invoke('hide');
          });
          
          // we hide every other other select tree ul on mouseover
          li.observe('mouseover', function() {
            tree.select('ul').invoke('hide');
          });
        }
        ul.insert(li);
      });
      tree.highlight();
    }
    /////////////////////////////
    
    // Every element is hidden, but preserves its width
    select.childElements().each(function(d) {
      d.setOpacity(0.01).setStyle({height: 0});
    });
    
    // Tree -------------
    tree = new Element('ul', {className: options.className, id: select.id+'_tree'});
    tree.display = function (e) {
      Event.stop(e);
      if (tree.empty()) {
        makeTree(select, tree);
      }
      search.focus();
      hideSelectTrees();
      reposition();
      tree.show();
      
      document.body.observe('mouseup', tree.undisplay);
    }
    
    tree.undisplay = function (e) {
      document.body.stopObserving('mouseup', tree.undisplay);
      tree.hide();
    }
    
    tree.highlight = function () {
      var selected = tree.select('.selected'),
          val = $V(select);
          
      selected.each(function(s) {
        s.removeClassName('selected');
      });
      if (val && (s = $(select.id+'_'+val))) {
        s.addClassName('selected');
      }
    }
    select.insert({after: tree.hide()});
    
    // List -------------
    list = new Element('ul').addClassName(options.className);
    list.id = select.id+'_list';
    
    list.navigate = function (e) {
      if (search.value) {
        var keycode = Event.key(e),
            focused = list.select('.focused');
        
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
      var li, text;
      list.update();
      if (s && s.length > 1) {
        s = s.toLowerCase();
        select.select('option').each(function (c) {
          text = c.text.toLowerCase();
          if (text.indexOf(s) != -1) {
            li = new Element('li').update(c.text.replace(new RegExp(s, "gi"), function($1){return '<span class="highlight">'+$1+'</span>'}));
            li.observe('click', function() {
              $V(select, c.value, true);
              tree.highlight();
              search.value = '';
              select.display(false);
            });
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
    
    search.catchKey = function (e) {
      var keycode = Event.key(e);

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
          focused.onclick(Event.stop(e));
        }
        search.value = null;
      }
    }
    
    search.display = function (e) {
      var keycode = Event.key(e);
      
      if (validKey(keycode) && keycode != 8 && keycode != 27) {
        select.hide();
        tree.undisplay();
        list.update().show();
        search.setStyle({position: 'relative', top: 0})
              .stopObserving('keydown', search.display);
      }
    }
    select.insert({after: search});
    
    // The search input to blur the select control and catch keys
    search.observe('keydown', search.display)
          .observe('keydown', list.navigate)
          .observe('keyup', search.catchKey);

    // Select
    select.writeAttribute('size', 1);
    
    select.display = function (show) {
      search.setStyle({position: 'absolute', top: '-1200px'});
      select.show();
      list.hide();
      if (show) tree.display();
      search.value = '';
      search.observe('keydown', search.display);
    }

    select.onclick = tree.display;
  },
  makeAutocomplete: function(element, options) {
    element = $(element);
    
    options = Object.extend({
      width: '100px'
    }, options);
    
    var selectedOption = element.options[element.selectedIndex],
        textInput = new Element('input', {type: 'text', style:'width:'+options.width})
                                 .addClassName('autocomplete')
                                 .writeAttribute('autocomplete', false),
        list = new Element('div').addClassName('autocomplete'),
        views = [], viewToValue = {};

    textInput.value = selectedOption.disabled ? "" : selectedOption.text;
    element.insert({after: textInput}).insert({after: list}).hide();
    
    $A(element.options).each(function(e){
      if (e.disabled) return;
      views.push(e.text);
      viewToValue[e.text] = e.value;
    });
    
    new Autocompleter.Local(textInput, list.identify(), views, {
      afterUpdateElement: function(text, li){ 
        $V(element, viewToValue[text.value]);
      }
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
