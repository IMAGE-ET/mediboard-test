/* $Id: object_selector.js 7167 2009-10-29 16:22:17Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7167 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

SourceToViewSender = {
  modal: null,
  
  edit: function(sender_id) {
	var url = new Url('system', 'ajax_form_source_to_view_sender');
    url.addParam('sender_id', sender_id);
    url.requestModal(400, 250);
    this.modal = url.modaleObject;
  },
  
  onSubmit: function(form) {
    return onSubmitFormAjax(form, { 
      onComplete: function() {
        ViewSender.refreshList();
        SourceToViewSender.modal.close();
      }
    } )
  },
	  
  confirmDeletion: function(form) {
	var options = {
      typeName:'lien export - source d\'export', 
      objName: $V(form.source_to_view_sender_id),
      ajax: 1
	}
	var ajax = {
      onComplete: function() {
        ViewSender.refreshList();
        SourceToViewSender.modal.close();
      }
    }
	
    confirmDeletion(form, options, ajax);		
  }
};