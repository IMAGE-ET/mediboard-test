/* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/** Helper Function for Caret positioning
 * @param element The form element (automatically added by Prototype, don't use it)
 * @param begin   Where the selection starts
 * @param end     Where the selection ends
 * @param value   The value replacing the selection
 * @return If no argument is provided, it returns the selection start and end
 *         If only start is provided, it puts the caret at the start position and returns an empty value
 *         If start and end are provided, it selects the character range and returns the selected string
 *         If value is provided, it returns the selected text and replaces it by value
 */
Element.addMethods(['input', 'textarea'], {
  caret: function (element, begin, end, value) {
    if (element.length == 0) return null;
    
    // Begin ?
    if (Object.isNumber(begin)) {
      // End ?
      end = (Object.isNumber(end)) ? end : begin;
      
      // Text replacement
      var selected = element.value.substring(begin, end);
      if (value) {
        var s;
        s = element.value.substring(0, begin) + 
            value + 
            element.value.substring(end, element.value.length);
        element.value = s;
      }
      
      // Gecko, Opera
      if(element.setSelectionRange) {
        element.focus();
        element.setSelectionRange(begin, value ? begin+value.length : end);
      }
      // IE
      else if (element.createTextRange) {
        var range = element.createTextRange();
        range.collapse(true);
        range.moveEnd('character', value ? begin+value.length : end);
        range.moveStart('character', begin);
        range.select();
      }

      return selected;
    }
    // No begin and end
    else {
      // Gecko, Opera
      if (element.setSelectionRange) {
        begin = element.selectionStart;
        end = element.selectionEnd;
      }
      // IE
      else if (document.selection && document.selection.createRange) {
        var range = document.selection.createRange();
        begin = 0 - range.duplicate().moveStart('character', -100000);
        end = begin + range.text.length;
      }
      return {begin:begin, end:end};
    }
  }
});

/** Input mask for text input elements 
 * @param element The form element (automatically added by Prototype, don't use it)
 * @param mask    The input mask as a string composed by [9, a, *, ~] by default
 * @param options Options : placeholder, 
 *                          charmap, 
 *                          completed (function called when the text is full)
 */
Element.addMethods('input', {
  mask: function(element, mask, options) {
    element.options = Object.extend({
      placeholder: "_",
      charmap: {
        '9':"[0-9]",
        'a':"[A-Za-z]",
        '*':"[A-Za-z0-9]",
        'x':"[A-Fa-f0-9]",
        '~':"[+-]"
      },
      completed: Prototype.emptyFunction,
      format: Prototype.K
    }, options);

    var maskArray = mask.toArray();
    var buffer = new Array(mask.length);
    var locked = new Array(mask.length);
    var valid = false;   
    var ignore = false; //Variable for ignoring control keys
    var firstNonMaskPos = null;
    
    var re = new RegExp("^"+
      maskArray.collect(function(c) {
        return element.options.charmap[c]||((/[A-Za-z0-9]/.match(c) ? "" : "\\" )+c);
      }).join('')+"$");

    //Build buffer layout from mask & determine the first non masked character
    maskArray.each(function(c, i) {
      locked[i] = Object.isUndefined(element.options.charmap[c]);
      buffer[i] = locked[i] ? c : element.options.placeholder;
      if(!locked[i] && firstNonMaskPos == null)
        firstNonMaskPos = i;
    });
    
    // The element size and maxlength are updated
    element.size = mask.length;
    element.maxLength = mask.length;
    
    // Add a placeholder
    function addPlaceholder (c, r) {
      element.options.charmap[c] = r;
    }
    
    // Focus event, called on element.onfocus
    function focusEvent(e) {
      checkVal();
      writeBuffer();
      var f = function() {
        valid ?
          Prototype.emptyFunction :///element.caret(0, mask.length):
          element.caret(firstNonMaskPos);
      };
      element.oldValue = element.value;
      f.defer();
    }
    focusEvent = focusEvent.bindAsEventListener(element);
    
    // Key down event, called on element.onkeydown
    function keydownEvent(e) {
      var pos = element.caret();
      var k = Event.key(e);
      ignore = ((k < 41) && (k != 32) && (k != 16)); // ignore modifiers, home, end, ... except space and shift
      
      //delete selection before proceeding
      if((pos.begin - pos.end) != 0 && (!ignore || k==8 || k==46)) { // if not ignored or is backspace or delete
        clearBuffer(pos.begin, pos.end);
      }
      
      //backspace and delete get special treatment
      switch (k) {
      case 8: // backspace
        while(pos.begin-- >= 0) {
          if(!locked[pos.begin]) {
            buffer[pos.begin] = element.options.placeholder;
            if(Prototype.Browser.Opera) {
              //Opera won't let you cancel the backspace, so we'll let it backspace over a dummy character.
              s = writeBuffer();
              element.value = s.substring(0, pos.begin)+" "+s.substring(pos.begin);
              element.caret(pos.begin+1);
            }
            else {
              writeBuffer();
              element.caret(Math.max(firstNonMaskPos, pos.begin));
            }
            return false;
          }
        }
      break;
      
      case 46: // delete
        clearBuffer(pos.begin, pos.begin+1);
        writeBuffer();
        element.caret(Math.max(firstNonMaskPos, pos.begin));
        return false;
      break;

      case 27: // escape
        clearBuffer(0, mask.length);
        writeBuffer();
        element.caret(firstNonMaskPos);
        return false;
      break;
      }
      
      return true;
    }
    keydownEvent = keydownEvent.bindAsEventListener(element);
    
    function keypressEvent(e) {
      if (ignore) {
        ignore = false;
        //Fixes Mac FF bug on backspace
        return (e.keyCode == 8) ? false : null;
      }
      
      e = e || window.event;
      var k = Event.key(e);

      if (e.ctrlKey || e.altKey || 
          (k == Event.KEY_TAB) || 
          (k >= Event.KEY_PAGEDOWN && k <= Event.KEY_DOWN)) return true; //Ignore
      
      var pos = element.caret();
      
      if ((k >= 41 && k <= 122) || k == 32 || k > 186) {//typeable characters
        var p = seekNext(pos.begin-1);

        if (p < mask.length) {
          var nRe = new RegExp(element.options.charmap[mask.charAt(p)]);
          var c = String.fromCharCode(k);

          if (c.match(nRe)) {
            buffer[p] = c;
            writeBuffer();
            var next = seekNext(p);
            element.caret(next);
            
            if (next == mask.length) {
              checkVal();
              element.options.completed(element);
            }
          }
        }
      }

      return false;
    }
    keypressEvent = keypressEvent.bindAsEventListener(element);
    
    function clearBuffer(start, end) {
      for(var i = start; i < end && i < mask.length; i++) {
        if(!locked[i]) buffer[i] = element.options.placeholder;
      }
    }
    
    function writeBuffer() {
      return element.value = buffer.join('');
    }
    
    function checkVal(fire) {
      var test = element.value;
      var pos = 0;
      
      for (var i = 0; i < mask.length; i++) {
        if(!locked[i]) {
          buffer[i] = element.options.placeholder;
          while(pos++ < test.length) {
            //Regex Test each char here.
            var reChar = new RegExp(element.options.charmap[mask.charAt(i)]);
            if (test.charAt(pos-1).match(reChar)) {
              buffer[i] = test.charAt(pos-1);
              break;
            }
          }
        }
      }
      checkVal = checkVal.bindAsEventListener(element);
      
      var s = writeBuffer();
      if (!s.match(re)) {
        s = element.value = "";
        clearBuffer(0, mask.length);
        valid = false;
      }
      else valid = true;
      
      if (fire) {
        if (element.oldValue != s && element.onchange) {
          element.onchange(element);
        }
        element.oldValue = element.value;
      }
    }
    
    function seekNext(pos) {
      while (++pos < mask.length) {
        if(!locked[pos]) return pos;
      }
      return mask.length;
    }
    
    element.observe("focus", focusEvent);
    element.observe("blur",  (function(){checkVal(true);}).bind(this));
    element.observe("mask:check", checkVal);
    element.onkeydown  = keydownEvent;
    element.onkeypress = keypressEvent;
    
    //Paste events for IE and Mozilla thanks to Kristinn Sigmundsson
    if (Prototype.Browser.IE)
      element.onpaste= function() {setTimeout(checkVal, 0);};     
    
    else if (Prototype.Browser.Gecko)
      element.addEventListener("input", checkVal, false);
      
    checkVal(); //Perform initial check for existing values
  }/*,
  
  unmask: function(element) {
    element.stopObserving("focus", element.focusEvent);
    element.stopObserving("blur",  element.checkVal);
    element.onkeydown  = null;
    element.onkeypress = null;
    
    if (Prototype.Browser.IE)
      element.onpaste = null;
    
    if (Prototype.Browser.Gecko)
      element.removeEventListener('input', element.checkVal, false);
  }*/
  , 
  getFormatted: function (element, mask, format) {
    var maskArray = mask.toArray();
    var reMask = "^";
    var prevChar = null;
    var count = 0;
    var charmap = null;

    if (element.options) {
      charmap = element.options.charmap;
    } else {
      return element.value;
    }
    
    for (i = 0; i <= maskArray.length; i++) {
      if (!maskArray[i]) { // To manage the latest char
        reMask += "("+charmap[prevChar]+"{"+count+"})";
        break;
      }
    
      var c = maskArray[i];

      if (!charmap[c]) {
        if (charmap[prevChar]) {
          reMask += "("+charmap[prevChar]+"{"+Math.max(1,count)+"})";
        }
        reMask += (((/[A-Za-z0-9]/.match(c) || c == null)) ? "" : "\\" )+c;
        prevChar = c;
        count = 0;
      }
      
      else if (prevChar != c) {
        if (charmap[prevChar]) {
	        reMask += "("+charmap[prevChar]+"{"+count+"})";
	        prevChar = c;
	        count = 0;
	      }
	      else {
	        prevChar = c;
	        count++;
	      }
      }
      
      else if (prevChar == c) {
        count++;
      }
      
    }

    reMask = new RegExp(reMask+"$");
    
    var matches = reMask.exec(element.value);
    if (matches) {
      if (!format) {
	      format = '';
	      for (i = 1; (i < matches.length && i < 10); i++) {
	        format += "$"+i;
	      }
	    }
	    for (i = 1; (i < matches.length && i < 10); i++) {
	      format = format.replace("$"+i, matches[i]);
	    }
	  } else {
	    format = element.value;
	  }
    return format;
  }
});

Element.addMethods({
  getLabel: function (element, strict) {
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
    
    var htmlFor = "", match;
    
    if (!strict && /radio|checkbox/i.test(element.type)){
      if (match = new RegExp("(\.*)_"+element.value+"$", "i").exec(element.id)) {
        htmlFor = "label[for='"+match[1]+"'], label[htmlFor='"+match[1]+"'], ";
      }
    }
    
    return $(element.form).down(htmlFor+"label[for='"+element.id+"'], label[htmlFor='"+element.id+"']");
  },
  
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
      
      if (!Prototype.Browser.WebKit) {
        element.setOpacity(0.4);
      }
      
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
      
      if (!Prototype.Browser.WebKit) {
        element.setOpacity(1);
      }
      
      document.stopObserving('mousemove', performDrag)
              .stopObserving('mouseup', endDrag);

      if (element.id) {
        cookie.setValue('ElementHeight', element.id, element.getHeight() - Math.round(grippie.getHeight()/2));
      }
    }
  }
});

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
          result = result.toFixed(options.decimals);
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
          result = result.toFixed(options.decimals);
        }
        result = ((options.showPlus && result >= 0)?'+':'')+result;
        
        $V(element, result, true);
        element.select();
      }
    };
    
    if (element.value && options.decimals !== null) {
      element.value = Number(element.value).toFixed(options.decimals);
    }
    
    var table = '<table class="control numericField"><tr><td style="padding:0;border:none;"></td><td class="arrows" style="padding:0;border:none;"><div class="up"></div><div class="down"></div></td></tr></table>';
    element.insert({before: table});
    table = element.previous();
    table.down('td').update(element);

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
    };
    
    var validKey = function (keycode) {
      return (keycode >= 48 && keycode <= 90 || // letters and digits
              keycode >= 96 && keycode <= 111 || // num pad
              keycode >= 186 && keycode <= 181 ||
              keycode >= 219 && keycode <= 222 ||
              keycode == 32 || // space
              keycode == 8); // backspace
    };
    
    var updateCoordinates = function () {
      pos = select.cumulativeOffset();
      dim = select.getDimensions();
      
      pos.left = pos.left+parseInt(select.getStyle('margin-left'))+'px';
      pos.top  = pos.top +parseInt(select.getStyle('margin-top'))-1+dim.height+'px';
    };
    
    var reposition = function () {
      updateCoordinates();
      var style = {zIndex: 40, position: 'absolute', left: pos.left, top: pos.top};
      tree.setStyle(style);
      list.setStyle(style);
    };
    
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
    };
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
    };
    
    tree.undisplay = function (e) {
      document.body.stopObserving('mouseup', tree.undisplay);
      tree.hide();
    };
    
    tree.highlight = function () {
      var selected = tree.select('.selected'),
          val = $V(select);
          
      selected.each(function(s) {
        s.removeClassName('selected');
      });
      if (val && (s = $(select.id+'_'+val))) {
        s.addClassName('selected');
      }
    };
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
    };
    
    list.search = function(s) {
      var li, text;
      list.update();
      if (s && s.length > 1) {
        s = s.toLowerCase();
        select.select('option').each(function (c) {
          text = c.text.toLowerCase();
          if (text.indexOf(s) != -1) {
            li = new Element('li').update(c.text.replace(new RegExp(s, "gi"), function($1){return '<span class="highlight">'+$1+'</span>';}));
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
    };
    
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
    };
    
    search.display = function (e) {
      var keycode = Event.key(e);
      
      if (validKey(keycode) && keycode != 8 && keycode != 27) {
        select.hide();
        tree.undisplay();
        list.update().show();
        search.setStyle({position: 'relative', top: 0})
              .stopObserving('keydown', search.display);
      }
    };
    
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
    };

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
