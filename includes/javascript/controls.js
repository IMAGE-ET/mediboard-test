function getKeycode(e) {
  return e.which || e.keyCode || window.event.keyCode || window.event.which;
}

//Helper Function for Caret positioning
Element.addMethods(['input', 'textarea'], {
  caret: function (element, begin, end) {
    if (element.length == 0) return null;
    
    if (Object.isNumber(begin)) {
      end = (Object.isNumber(end)) ? end : begin;
      
      if(element.setSelectionRange) {
        element.focus();
        element.setSelectionRange(begin, end);
      }
      else if (element.createTextRange) {
        var range = element.createTextRange();
        range.collapse(true);
        range.moveEnd('character', end);
        range.moveStart('character', begin);
        range.select();
      }
      return null;
    }
    else {
      if (element.setSelectionRange) {
        begin = element.selectionStart;
        end = element.selectionEnd;
      }
      else if (document.selection && document.selection.createRange) {
        var range = document.selection.createRange();
        begin = 0 - range.duplicate().moveStart('character', -100000);
        end = begin + range.text.length;
      }
      return {begin:begin, end:end};
    }
  }
});

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
      completed: Prototype.emptyFunction
    }, options || {});

    var maskArray = mask.toArray();
    var buffer = new Array(mask.length);
    var locked = new Array(mask.length);
    var valid = false;   
    var ignore = false; //Variable for ignoring control keys
    var firstNonMaskPos = null;
    element.rawvalue = null;
    
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
    
    function addPlaceholder (c, r) {
      element.options.charmap[c] = r;
    }
    
    function updateRawValue() {
      element.rawvalue = null;
      buffer.each(function(c, i) {
        if (!locked[i] && (c != element.options.placeholder)) {
          element.rawvalue = (element.rawvalue || '') +  c;
        }
      });
    }
    
    function focusEvent(e) {
      checkVal();
      writeBuffer();
      var f = function() {
        valid ?
          element.caret(0, mask.length):
          element.caret(firstNonMaskPos);
      };
      f.defer();
    }
    focusEvent = focusEvent.bindAsEventListener(element);
    
    function keydownEvent(e) {
      var pos = element.caret();
      var k = getKeycode(e);
      ignore = ((k < 41) && (k != 32) && (k != 16));
      
      //delete selection before proceeding
      if((pos.begin - pos.end) != 0 && (!ignore || k==8 || k==46)) {
        clearBuffer(pos.begin, pos.end);
      }
      
      //backspace and delete get special treatment
      switch (k) { //backspace
      case 8:
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
      
      case 46: //delete
        clearBuffer(pos.begin, pos.begin+1);
        writeBuffer();
        element.caret(Math.max(firstNonMaskPos, pos.begin));
        return false;
      break;

      case 27: //escape
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
        //return (e.keyCode == 8) ? false : null;
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
      element.value = buffer.join('');
      return element.value;
    }
    
    function checkVal() {
      updateRawValue();
      
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
        element.value = "";
        clearBuffer(0, mask.length);
        valid = false;
      }
      else valid = true;
    }
    
    function seekNext(pos) {
      while (++pos < mask.length) {
        if(!locked[pos]) return pos;
      }
      return mask.length;
    }
    
    element.observe("focus", focusEvent);
    element.observe("blur",  checkVal);
    element.onkeydown  = keydownEvent;
    element.onkeypress = keypressEvent;
    
    //Paste events for IE and Mozilla thanks to Kristinn Sigmundsson
    if (Prototype.Browser.IE)
      element.onpaste = checkVal.defer();
    
    if (Prototype.Browser.Gecko)
      element.addEventListener('input', checkVal, false);
      
    checkVal();//Perform initial check for existing values
  },
  
  unmask: function(element) {
    element.onfocus    = null;
    element.onblur     = null;
    element.onkeydown  = null;
    element.onkeypress = null;
    
    if (Prototype.Browser.IE)
      element.onpaste = null;
    
    if (Prototype.Browser.Gecko)
      element.removeEventListener('input', element.checkVal, false);
  }
});
