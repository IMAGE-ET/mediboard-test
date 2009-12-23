/* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
function refreshValue(element, klass, id, field) {
  if (id && $(element)) {
    var url = new Url("dPstock", "httpreq_vw_object_value");
    url.addParam("class", klass);
    url.addParam("id", id);
    url.addParam("field", field);
    url.requestUpdate(element);
  }
}

/** Submit order function 
 *  Used to submit an order : new or edit order
 *  @param oForm The form containing all the info concerning the order to submit
 *  @param options Options used to execute functions after the submit : {refreshLists, close}
 */
function submitOrder (oForm, options) {
  options = Object.extend({
    close: false,
    confirm: false,
    refreshLists: false
  }, options);
  
  if (!options.confirm || (options.confirm && confirm('Voulez-vous vraiment effectuer cette action ?'))) {
    return onSubmitFormAjax(oForm, {
      onComplete: function() {
        if (options.close && window.opener) {
          window.close();
        } else {
          refreshOrder($V(oForm.order_id), options);
        }
        if (options.refreshLists) {
          refreshLists();
        }
      }
    });
  }
  else return false;
}

/** Submit order item function
 *  Used to submit an order item : new or edit order item
 *  @param oForm The form containing all the info concerning the order item to submit
 *  @param options Options used to execute functions after the submit : {refreshLists, close}
 */
function submitOrderItem (oForm, options) {
  if (options && options.noAjax) {
    oForm.submit();
  } else {
    onSubmitFormAjax(oForm, {
      onComplete: function() {
        refreshOrder(oForm.order_id.value, options);
        if (!options.noRefresh) {
          refreshOrderItem($V(oForm.order_item_id), options);
        }
      }
    });
  }
}

/** The refresh order function
 *  Used to refresh the view of an order
*/
function refreshOrder(order_id, options) {
  if (options && options.refreshLists) {
    refreshLists();
  }
  var url = new Url("dPstock","httpreq_vw_order");
  url.addParam("order_id", order_id);
  url.requestUpdate("order-"+order_id);
}

function refreshOrderItem(order_item_id) {
  var url = new Url("dPstock", "httpreq_vw_order_item");
  url.addParam("order_item_id", order_item_id);
  url.requestUpdate("order-item-"+order_item_id);
}

var orderTypes = ["waiting", "locked", "pending", "received", "cancelled"];

function refreshListOrders(type, keywords) {
  var url = new Url("dPstock","httpreq_vw_orders_list");
  url.addParam("type", type);
  url.addParam("keywords", keywords);
  url.requestUpdate("list-orders-"+type);
  
  return false;
}

function refreshLists(keywords) {
  if (!window.opener) {
    // We load the visible one first
    orderTypes.each(function(type){
      if ($("list-orders-"+type).visible()) {
        refreshListOrders(type, keywords);
      }
    });
    orderTypes.each(function(type){
      if (!$("list-orders-"+type).visible()) {
        refreshListOrders(type, keywords);
      }
    });
  } else if (window.opener.refreshLists) {
    window.opener.refreshLists();
  }
  return false;
}

function popupOrder(iOrderId, width, height, bAutofill) {
  width = width || 900;
  height = height || 700;

  var url = new Url("dPstock", "vw_aed_order");
  url.addParam("order_id", iOrderId);
  if (bAutofill) {
    url.addParam("_autofill", 1);
  }

  url.popup(width, height, "Edition de commande");
}

function popupReception(iOrderId, width, height) {
  width = width || 1000;
  height = height || 800;

  var url = new Url("dPstock", "vw_edit_reception");
  url.addParam("order_id", iOrderId);
  url.popup(width, height, "Réception de commande");
}

function popupOrderForm(iOrderId, width, height) {
  width = width || 900;
  height = height || 700;

  var url = new Url("dPstock", "vw_order_form");
  url.addParam("order_id", iOrderId);
  url.popup(width, height, "Bon de commande");
}

function printBarcodeGrid(order_id, receptions_list, force_print) {
  var url = new Url("dPstock", "print_reception_barcodes");
  url.addParam("order_id", order_id);
  url.addParam("receptions_list", receptions_list);
  url.addParam("force_print", force_print);
  url.addParam("suppressHeaders", true);
  url.popup(800, 700, "Codes barres");
}