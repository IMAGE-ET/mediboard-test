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
  },

  verifyAvaibility : function(element) {
    element.src   = "style/mediboard/images/icons/loading.gif";
    new Url("eai", "ajax_get_tunnel_status")
      .addParam("source_guid", element.get('guid'))
      .requestJSON(function(status) {
        var title = element.title;
        element.title = "";
        element.src = "images/icons/status_red.png";
        if (status.reachable === "1") {
          element.src = "images/icons/status_green.png";
        }
        element.onmouseover = function() {
          ObjectTooltip.createDOM(element,
            DOM.div(null,
              DOM.table({className:"main tbl", style:"max-width:350px"},
                DOM.tr(null,
                  DOM.th(null, title)
                ),
                DOM.tr(null,
                  DOM.td({className:"text"},
                    DOM.strong(null, "Message : "), status.message)
                )
              )
            ).hide())
        };
      })
  }
};