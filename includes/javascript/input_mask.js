/*
 * Based on LGPL works of :
 * - Baron Schwartz <baron at xaprb dot com> (2006)
 * - Mihai Bazon, http://www.bazon.net/mishoo (2006)
 */
 
 
var is_gecko = /gecko/i.test(navigator.userAgent);
var is_ie    = /MSIE/.test(navigator.userAgent);

function setSelectionRange(input, start, end) {
	if (is_gecko) {
		input.setSelectionRange(start, end);
	} else {
		// assumed IE
		var range = input.createTextRange();
		range.collapse(true);
		range.moveStart("character", start);
		range.moveEnd("character", end - start);
		range.select();
	}
};

function getSelectionStart(input) {
	if (is_gecko)
		return input.selectionStart;
	var range = document.selection.createRange();
	var isCollapsed = range.compareEndPoints("StartToEnd", range) == 0;
	if (!isCollapsed)
		range.collapse(true);
	var b = range.getBookmark();
	return b.charCodeAt(2) - 2;
};

function getSelectionEnd(input) {
	if (is_gecko)
		return input.selectionEnd;
	var range = document.selection.createRange();
	var isCollapsed = range.compareEndPoints("StartToEnd", range) == 0;
	if (!isCollapsed)
		range.collapse(false);
	var b = range.getBookmark();
	return b.charCodeAt(2) - 2;
};
 
InputMask = {

  /* Each mask has a format and regex property.  The format consists
   * of spaces and non-spaces.  A space is a placeholder for a value the user
   * enters.  A non-space is a literal character that gets copied to that
   * position in the value.  The regex is used to validate each character, one
   * at a time (it is not applied against the entire value in the form field,
   * just the characters the user enters).
   *
   * The way you name your masks is significant.  If you create a mask called
   * date_us, you cause it to be applied to a form field by a) adding the
   * input_mask class to that form field, which triggers this script to treat
   * it specially, and b) adding the class mask_date_us to the form field,
   * which causes this script to apply the date_us mask to it.
   */
  masks: {
    telSuisse: {
      format: '  -  -  -  ',
      regex:  /\d/
    },
    telFrance: {
      format: '  -  -  -  -  ',
      regex:  /\d/
    }
  },
  
  masksEx: {
    telSuisse: "### ### ## ##",
    telFrance: "## ## ## ## ##",
  },

  /* Finds every element with class input_mask and applies masks to them.
   */
  setupElementMasks: function() {
    document.getElementsByClassName('mask| ').each(function(item) {
      Event.observe(item, 'keypress', InputMask.cacheFormerValue.bindAsEventListener(item));
      Event.observe(item, 'keyup', InputMask.applyMaskEx.bindAsEventListener(item));
    } );
  },
  
  showValue: function(event) {
    Console.debug(event.type, "Event type");
    Console.debug(this.value, "Element value");
  },
  
  /* This is triggered when the key is pressed in the form input.  It is
   * bound to the element, so 'this' is the input element.
   */
  applyMask: function(event) {
    var match = /_(\w+)/.exec(this.className);
    if ( match.length == 2 && InputMask.masks[match[1]] ) {
      var mask = InputMask.masks[match[1]];
      var key  = InputMask.getKey(event);

      if ( InputMask.isPrintable(key) ) {
        var ch    = String.fromCharCode(key);
        var str    = this.value + ch;
        var pos    = str.length;
        if ( mask.regex.test(ch) && pos <= mask.format.length ) {
          if ( mask.format.charAt(pos - 1) != ' ' ) {
            str = this.value + mask.format.charAt(pos - 1) + ch;
          }
          this.value = str;
        }
        Event.stop(event);
      }
    }
  },
  
  cacheFormerValue: function(event) {
    InputMask.formerValue = this.value;
  },
  
  applyMaskEx: function(event) {
    var start = getSelectionStart(this);
    var match = /_(\w+)/.exec(this.className);
    if (match.length != 2 || !InputMask.masks[match[1]] ) {
      return;
    }
    
    // Builds partial masks and values
    var fullMask = InputMask.masksEx[match[1]];
    var valueMask = fullMask.replace(/[^#]/g, "");
    var tentativeValue = this.value.replace(/[\D]/g, "").substr(0, valueMask.length);
		var partialMask = valueMask.substr(0, tentativeValue.length);

    // Checks for partial value
    var partialReg = new RegExp("^" + partialMask.replace(/#/g, "(\\d)") + "$");
    if (!partialReg.test(tentativeValue)) {
      this.value = InputMask.formerValue;
      Event.stop(event);
      return;
    }
    
    // Apply partial mask
    var matches = tentativeValue.match(partialReg);
    matches.shift();
    matches.each(function (value) {
      fullMask = fullMask.replace("#", value);
    } );
    
    var lastNotFilled = fullMask.indexOf("#");
    if (-1 != lastNotFilled) {
      fullMask = fullMask.substr(0, lastNotFilled);
    }
    this.value = fullMask;
  },

  /* Returns true if the key is a printable character.
   */
  isPrintable: function(key) {
    return ( key >= 32 && key < 127 );
  },

  /* Returns the key code associated with the event.
   */
  getKey: function(e) {
    return window.event ? window.event.keyCode
        : e        ? e.which
        :           0;
  }
};