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
  }

};
