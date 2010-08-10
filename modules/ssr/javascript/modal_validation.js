/* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7795 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

ModalValidation = {
  window: null,
	kine_id: null,
	
	form: function() {
		return getForm("editSelectedEvent");
	},
	
	toggleSejour: function(sejour_id) {
		$('list-evenements-modal').select('input.CSejour-'+sejour_id).each(function(checkbox) {
			if (!checkbox.disabled) {
        checkbox.checked = !checkbox.checked;
			}
    });
	},
  
	select: function() {
	  var event_ids = [];
	  $$(".event.selected").each(function(e){
	    var matches = e.className.match(/CEvenementSSR-([0-9]+)/);
	    if (matches) {
	      event_ids.push(matches[1]);
	    }
	  });
	  
    var form = this.form();
	  $V(form.event_ids, event_ids.join('|'));
    return $V(form.event_ids);
	},
	
	selectCheckboxes: function() {
    var event_ids = [];
    $('list-evenements-modal').select('input[type="checkbox"]').each(function(checkbox) {
			if (checkbox.checked) {
        event_ids.push(checkbox.value);
			}
    });
    var form = this.form();
    $V(form.event_ids, event_ids.join('|'));
    return $V(form.event_ids);
	},
	
	set: function(values) {
    Form.fromObject(this.form(), values);
	},
	
	submit: function() {
    if(!this[$('modal_evenements').empty() ? "select" : "selectCheckboxes"]()) {
      return;
    }

	  return onSubmitFormAjax(this.form(), { onComplete: function() { 
      PlanningTechnicien.show(this.kine_id, null, null, 650, true);
      ModalValidation.close();
	  } });
	},
	
  update: function() {
		this.select();
    this.open();

    var form = this.form();
    var url = new Url("ssr", "ajax_update_modal_evenements");
    url.addParam("token_field_evts", $V(form.event_ids));
    url.requestUpdate("modal_evenements", { 
      onComplete: function() {
        // Positioning takes a lot of time for big modals with IE8-
        if (!Prototype.Browser.IE || document.documentMode > 8) {
          ModalValidation.window.position();
        }
      }
    });
  },

  open: function(){
    // Forced size, cuz positioning takes a lot of time for big modals with IE8-
    if (Prototype.Browser.IE && document.documentMode <= 8) {
      $('modal_evenements').setStyle({
        width: '700px',
        height: '380px'
      })
    }
    
    this.window = modal($('modal_evenements'), {
      className: 'modal'
    });
  },
  
  close: function() {
    this.window.close();
    $('modal_evenements').update();
  }
}
