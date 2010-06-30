/* $Id: object_selector.js 7167 2009-10-29 16:22:17Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7167 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

ObjectMerger = {
	setField: function(field, source) {
	  var form = source.form;
	  var value = $V(source);
	  var field = $(form.elements[field]);
	
	  // Update Value
	  $V(field, value);
	  
	  if (!field.hasClassName) {
	    return;
	  }
	
	  var view = null;
	  var props = field.getProperties();
	  
	  // Can't we use Calendar.js helpers ???
	  if (props.dateTime) {
	    view = $(form.elements[field.name+'_da']);
	    $V(view, value ? Date.fromDATETIME(value).toLocaleDateTime() : "");
	  }
	  
	  if (props.date) {
	    view = $(form.elements[field.name+'_da']);
	    $V(view, value ? Date.fromDATE(value).toLocaleDate() : "");
	  }
	
	  if (props.time) {
	    view = $(form.elements[field.name+'_da']);
	    $V(view, value);
	  }

    var label = Element.getLabel(source);
	  if (props.ref) {
	    view = $(form.elements["_"+field.name+'_view']);
	    $V(view, (label.textContent || label.innerText).strip());
	  }
	
	  if (props.mask) {
	    $V(field, (label.textContent || label.innerText).strip(), false);
	  }
	},

  updateOptions: function(field) {
	  var form = field.form;
	  $A(form.elements["_choix_"+field.name]).each(function (element) {
	    element.checked = element.value.stripAll() == field.value.stripAll();
	  } );
	},
	
	confirm: function(fast) {
	  $V(getForm("form-merge").fast, fast);
	  Modal.confirm($('confirm-'+fast), { onOK: ObjectMerger.perform } );
	  return false;
	},
	
	perform: function() {
	  getForm("form-merge").submit();
	}
};
