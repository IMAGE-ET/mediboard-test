//Helper Function for Caret positioning
Element.addMethods('input', {
  caret: function (element, begin, end) {
    if (element.length == 0) return null;
    
    if (Object.isNumber(begin)) {
      end = (Object.isNumber(end))?end:begin;  
      
      if(element.setSelectionRange){
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
  addPlaceholder: function(element, c, r) {
    element.options.charmap[c] = r;
  },
  
  mask: function(element, mask, options) {
    element.options = Object.extend(options, {
      placeholder: "_",
      charmap: {
        '9':"[0-9]",
        'a':"[A-Za-z]",
        '*':"[A-Za-z0-9]"
      },
      completed: null
    });

    var maskArray = mask.toArray();
    var buffer = new Array(mask.length);
    var locked = new Array(mask.length);
    var valid = false;   
    var ignore = false;       //Variable for ignoring control keys
    var firstNonMaskPos = null; 
    
    var re = new RegExp("^"+  
    maskArray.collect(function(c,i) {           
      return element.options.charmap[c]||((/[A-Za-z0-9]/.test(c)?"":"\\")+c);
    }).join('')+        
    "$");
    
    //Build buffer layout from mask & determine the first non masked character      
    maskArray.each(function(i,c) {        
      locked[i] = (element.options.charmap[c] == null);       
      buffer[i] = locked[i] ? c : options.placeholder;                 
      if(!locked[i] && firstNonMaskPos == null)
        firstNonMaskPos = i;
    });
    
    function focusEvent() {          
      checkVal();
      writeBuffer();
      var c = function() {
        element.caret(valid ? mask.length : firstNonMaskPos);         
      };
      c.defer();
    };
    
    function keydownEvent(e) {       
      var pos = element.caret();
      var k = e.keyCode;
      ignore = (k < 16 || (k > 16 && k < 32 ) || (k > 32 && k < 41));
      
      //delete selection before proceeding
      if((pos.begin - pos.end)!=0 && (!ignore || k==8 || k==46)){
        clearBuffer(pos.begin, pos.end);
      } 
      //backspace and delete get special treatment
      if(k == 8) {//backspace          
        while(pos.begin-- >= 0) {
          if(!locked[pos.begin]) {               
            buffer[pos.begin] = options.placeholder;
            if(Prototype.Browser.Opera) {
              //Opera won't let you cancel the backspace, so we'll let it backspace over a dummy character.               
              s = writeBuffer();
              element.value = s.substring(0,pos.begin)+" "+s.substring(pos.begin);
              element.caret(pos.begin+1);               
            }
            else {
              writeBuffer();
              element.caret(Math.max(firstNonMaskPos, pos.begin));
            }                 
            return false;               
          }
        }           
      }
      else if (k == 46) { //delete
        clearBuffer(pos.begin, pos.begin+1);
        writeBuffer();
        element.caret(Math.max(firstNonMaskPos, pos.begin));         
        return false;
      }
      else if (k == 27) { //escape
        clearBuffer(0, mask.length);
        writeBuffer();
        element.caret(firstNonMaskPos);         
        return false;
      }                 
    };
    
    function keypressEvent(e){          
      if (ignore) {
        ignore = false;
        //Fixes Mac FF bug on backspace
        return (e.keyCode == 8) ? false : null;
      }
      e = e || window.event;
      var k = e.charCode || e.keyCode || e.which;           
      var pos = element.caret();
              
      if (e.ctrlKey || e.altKey) {//Ignore
        return true;
      }
      else if ((k>=41 && k<=122) ||k==32 || k>186){//typeable characters
        var p = seekNext(pos.begin-1);          
        if (p < mask.length) {
          if (new RegExp(element.options.charmap[mask.charAt(p)]).test(String.fromCharCode(k))) {
            buffer[p] = String.fromCharCode(k);
            writeBuffer();
            var next = seekNext(p);
            element.caret(next);
            if (options.completed && next == mask.length)
              options.completed.call($F(element));
          }
        }
      }       
      return false;       
    };
    
    function clearBuffer(start, end){
      for(var i = start; i < end && i < mask.length; i++) {
        if(!locked[i])
          buffer[i] = options.placeholder;
      }
    };
    
    function writeBuffer(){       
      return element.value = buffer.join('');        
    };
    
    function checkVal(){  
      //try to place charcters where they belong
      var test = $F(element);
      var pos = 0;
      for (var i = 0; i < mask.length; i++) {         
        if(!locked[i]){
          buffer[i] = options.placeholder;
          while(pos++ < test.length){
            //Regex Test each char here.
            var reChar = new RegExp(element.options.charmap[mask.charAt(i)]);
            if (test.charAt(pos-1).match(reChar)) {
              buffer[i] = test.charAt(pos-1);
              break;
            }                 
          }
        }
      }
      var s = writeBuffer();
      if (!s.match(re)) {             
        element.value = "";  
        clearBuffer(0,mask.length);
        valid = false;
      }
      else valid = true;
    };
    
    function seekNext(pos) {       
      while (++pos < mask.length){         
        if(!locked[pos]) return pos;
      }
      return mask.length;
    };
    
    element.observe("focus",   focusEvent);
    element.observe("blur",    checkVal);
    element.observe("keydown", keydownEvent);
    element.observe("keypress",keypressEvent);
    
    //Paste events for IE and Mozilla thanks to Kristinn Sigmundsson
    if (Prototype.Browser.IE) 
      element.onpaste = checkVal.defer();
    else if (Prototype.Browser.Gecko)
      element.addEventListener('input', checkVal, false);
      
    checkVal();//Perform initial check for existing values
  },
  
  unmask: function(element) {
    element.stopObserving("focus",   element.focusEvent);
    element.stopObserving("blur",    element.checkVal);
    element.stopObserving("keydown", element.keydownEvent);
    element.stopObserving("keypress",element.keypressEvent);
    if (Prototype.Browser.IE) 
      element.onpaste = null;
    else if (Prototype.Browser.Gecko)
      element.removeEventListener('input', element.checkVal, false);
  }
});
