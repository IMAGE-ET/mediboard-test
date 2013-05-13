/* $Id: object_selector.js 7167 2009-10-29 16:22:17Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7167 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

Message = {
  edit: function(message_id) {
    var url = new Url('system', 'ajax_form_message');
    url.addParam('message_id', message_id);
    url.requestModal(500);
  },

  onSubmit: function(form) {
    return onSubmitFormAjax(form, { 
      onComplete: function() {
        Message.refreshList();
        Control.Modal.close();
      }
    })
  },

  createUpdate: function() {
    var url = new Url('system', 'ajax_form_message_update');
    url.requestModal(500);
  },

  onSubmitUpdate: function(form) {
    if (!checkForm(form)) {
      return false;
    }
    
    Control.Modal.close();
    
    var url = new Url('system', 'ajax_form_message');
    url.addElement(form._update_moment);
    url.addElement(form._update_initiator);
    url.addElement(form._update_benefits);
    url.requestModal(500);

    return false;
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
    };
    
    var ajax = {
      onComplete: function() {
        Message.refreshList();
        Control.Modal.close();
      }
    };
    
    confirmDeletion(form, options, ajax);    
  },
  
  refreshList: function() {
    var url = new Url('system', 'ajax_list_messages');
    url.requestUpdate('list-messages');
  }
};
