/* $Id: object_selector.js 7167 2009-10-29 16:22:17Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7167 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

LongRequestLog = {
  refresh: function() {
    var form = getForm("Filter-Log");
    var url = new Url('system', 'ajax_list_long_request_logs');
    url.addFormData(form);
    url.requestUpdate('list-logs');

    return false;
  },

  edit: function(log_id) {
    var options = {
      onClose: LongRequestLog.refresh
    };

    new Url('system', 'edit_long_request_log') .
      addParam('log_id', log_id).
      requestModal(-100, null, options);
  },

  confirmDeletion: function(form) {
    var options = {
      typeName: 'log',
      objName: $V(form.long_request_log_id)
    };

    confirmDeletion(form, options, Control.Modal.close);
  },

  showPurge : function(form) {
    var url = new Url('system', 'vw_purge_long_request_logs');
    url.addElement(form.elements.user_id);
    url.addElement(form.elements.duration);
    url.addElement(form.elements.duration_operand);
    url.addElement(form.elements._date_min);
    url.addElement(form.elements._date_max);
    url.requestModal(800);
  },

  purgeSome : function(form, just_count) {
    var url = new Url('system', 'do_purge_long_request_logs', 'dosql');
    url.addElement(form.elements._date_min);
    url.addElement(form.elements._date_max);
    url.addElement(form.elements.user_id);
    url.addElement(form.elements.duration);
    url.addElement(form.elements.duration_operand);
    url.addElement(form.elements.purge_limit);

    if (just_count) {
      url.addParam('just_count', 1);
    }

    // Give some rest to server
    var onComplete = $('clean_auto').checked ? LongRequestLog.purgeSome.curry(form, just_count) : Prototype.emptyFunction;
    url.requestUpdate("resultPurgeLogs", {method: 'post', onComplete: function () { onComplete.delay(2); } });
  }
};
