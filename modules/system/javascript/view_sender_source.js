/* $Id: object_selector.js 7167 2009-10-29 16:22:17Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7167 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

ViewSenderSource = {
  modal: null,

  edit: function(sender_source_id) {
    var url = new Url('system', 'ajax_form_view_sender_source');
    url.addParam('sender_source_id', sender_source_id);
    url.requestModal(700);
    ViewSenderSource.modal = url.modaleObject;
    ViewSenderSource.modal.observe("afterClose", function(){ ViewSenderSource.refreshList(); });
  },

  onSubmit: function(form) {
    return onSubmitFormAjax(form, { 
	  onComplete: function() {
	    ViewSenderSource.modal.close();
	    ViewSenderSource.edit($V(form.source_id));
	  }
    })
  },

  confirmDeletion: function(form) {
	var options = {
      typeName:'source d\'export', 
      objName: $V(form.name),
      ajax: 1
	}
	var ajax = {
      onComplete: function() {
        ViewSenderSource.refreshList();
        ViewSenderSource.modal.close();
      }
    }
	
    confirmDeletion(form, options, ajax);		
  },
		
  refreshList: function() {
	var url = new Url('system', 'ajax_list_view_sender_sources');
	url.requestUpdate('list-sources');
  }
};
