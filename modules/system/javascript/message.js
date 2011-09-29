/* $Id: object_selector.js 7167 2009-10-29 16:22:17Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7167 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

Message = {
  modal: null,
  
  edit: function(sender_id) {
    var url = new Url('system', 'ajax_form_message');
    url.addParam('message_id', sender_id);
    url.requestModal(400);
    this.modal = url.modalObject;
  },

  onSubmit: function(form) {
    return onSubmitFormAjax(form, { 
      onComplete: function() {
        Message.refreshList();
        Message.modal.close();
      }
    })
  },

  duplicate: function(form) {
    $V(form.message_id, '');
    $V(form.titre, 'copie de ' + $V(form.titre));
  },
  
  confirmDeletion: function(form) {
    var options = {
      typeName:'message', 
      objName: $V(form.titre),
      ajax: 1
    }
    
    var ajax = {
      onComplete: function() {
        Message.refreshList();
        Message.modal.close();
      }
    }
    
    confirmDeletion(form, options, ajax);    
  },
  
  refreshList: function() {
    var url = new Url('system', 'ajax_list_messages');
    url.requestUpdate('list-messages');
  }
};
