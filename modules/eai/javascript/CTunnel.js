/**
 * $Id$
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CTunnel = {

  ajax : {
    onComplete: Control.Modal.close
  },

  proxyAction : function(action, id) {
    new Url("eai", "ajax_result_proxy")
      .addParam("action", action)
      .addParam("idTunnel", id)
      .requestUpdate("result_action");
  },

  editTunnel : function($id) {
    new Url("eai", "ajax_edit_tunnel")
      .addParam('tunnel_id', $id)
      .requestModal()
      .modalObject.observe("afterClose", CTunnel.refreshList);
  },

  refreshList : function () {
    new Url("eai", "ajax_refresh_list_tunnel")
      .requestUpdate("listTunnel");
  },

  submit : function(form) {
    return onSubmitFormAjax(form, this.ajax);
  },

  confirmDeletion : function(form, options) {

    confirmDeletion(form, options, this.ajax);
  }
};