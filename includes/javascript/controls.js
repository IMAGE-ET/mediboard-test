/* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

function getKeycode(e) {
  return (window.event && (window.event.keyCode || window.event.which)) || e.which || e.keyCode || false;
}

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
      var k = getKeycode(e);
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
      var k = getKeycode(e);

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
          console.debug('change');
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
    element.observe("blur",  (function(){checkVal(true)}).bind(this));
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
          reMask += "("+charmap[prevChar]+"{"+count+"})";
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
