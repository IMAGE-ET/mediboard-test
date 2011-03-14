/* $Id: object_selector.js 7167 2009-10-29 16:22:17Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7167 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

ViewSender = {
	modal: null,
	
	edit: function(sender_id) {
    var url = new Url('system', 'ajax_form_view_sender');
		url.addParam('sender_id', sender_id);
    url.requestModal(400);
	  this.modal = url.modaleObject;
	},

  onSubmit: function(form) {
    return onSubmitFormAjax(form, { 
		  onComplete: function() {
				ViewSender.refreshList();
				ViewSender.modal.close();
			}
		} )
	},
	
	confirmDeletion: function(form) {
		var options = {
      typeName:'export', 
      objName: $V(form.name),
      ajax: 1
		}
		var ajax = {
      onComplete: function() {
        ViewSender.refreshList();
        ViewSender.modal.close();
      }
		}
		
    confirmDeletion(form, options, ajax);		
	},
	
  refreshList: function() {
		var url = new Url('system', 'ajax_list_view_senders');
		url.requestUpdate('list-senders');
	}
};
