/* $Id: object_selector.js 7167 2009-10-29 16:22:17Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7167 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

ViewSender = {
  status_images : ["images/icons/status_red.png", "images/icons/status_orange.png", "images/icons/status_green.png"],
  modal: null,
  senders: {},
  
  edit: function(sender_id) {
    var url = new Url('system', 'ajax_form_view_sender');
    url.addParam('sender_id', sender_id);
    url.requestModal(400);
    this.modal = url.modalObject;
  },

  show: function(sender_id) {
    var url = new Url();
    url.mergeParams(this.senders[sender_id]);
    url.addParam('dialog', '1');
    url.popup(1000, 700);
  },

  onSubmit: function(form) {
    return onSubmitFormAjax(form, { 
      onComplete: function() {
        ViewSender.refreshList();
        ViewSender.modal.close();
      }
    })
  },

  duplicate: function(form) {
    $V(form.sender_id, '');
    $V(form.active, '0');
    $V(form.name, 'copie de ' + $V(form.name));
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
  },
  
  refreshMonitor: function() {
    var url = new Url('system', 'ajax_monitor_senders');
    url.requestUpdate('monitor');
  },

  doSend: function(username, password) {
    var url = new Url('system', 'ajax_send_views');
    if (username) url.addParam("username", username);
    if (password) url.addParam("password", password);
    url.requestUpdate('dosend');
	return false;
  },
  
  resfreshImageStatus : function(element){
    if (!element.get('id')) {
      return;
    }

    var url = new Url("system", "ajax_get_source_status");
    
    element.title = "";
    element.src   = "style/mediboard/images/icons/loading.gif";
    
    url.addParam("source_guid", element.get('guid'));
    url.requestJSON(function(status) {
      element.src = ViewSender.status_images[status.reachable];
    });
  }
};
