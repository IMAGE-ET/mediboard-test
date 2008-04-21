/** Submit order function 
 *  Used to submit an order : new or edit order
 *  @param oForm The form containing all the info concerning the order to submit
 *  @param options Options used to execute functions after the submit : {refreshLists, close}
 */
function submitOrder (oForm, options) {
  var oOptions = {
    close: false,
    confirm: false,
    refreshLists: false
  };
  
  Object.extend(oOptions, options);
  
  if (!oOptions.confirm || (oOptions.confirm && confirm('Voulez-vous vraiment effectuer cette action ?'))) {
    submitFormAjax(oForm, 'systemMsg',{
      onComplete: function() {
        if (oOptions.close && window.opener) {
          window.close();
        } else {
          refreshOrder($F(oForm.order_id), oOptions);
        }
      }
    });
  }
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
    submitFormAjax(oForm, 'systemMsg',{
      onComplete: function() {
        refreshOrder($F(oForm.order_id), options); 
        refreshOrderItem($F(oForm.order_item_id));
      }
    });
  }
}

/** The refresh order function
 *  Used to refresh the view of an order
*/
function refreshOrder(order_id, options) {
  if (options.refreshLists) {
    refreshLists();
  }
  url = new Url;
  url.setModuleAction("dPstock","httpreq_vw_order");
  url.addParam("order_id", order_id);
  url.requestUpdate("order-"+order_id, { waitingText: null } );
}

function refreshOrderItem(order_item_id) {
  url = new Url;
  url.setModuleAction("dPstock", "httpreq_vw_order_item");
  url.addParam("order_item_id", order_item_id);
  url.requestUpdate("order-item-"+order_item_id, { waitingText: null } );
}

function refreshListOrders(type, keywords) {
  url = new Url;
  url.setModuleAction("dPstock","httpreq_vw_orders_list");
  url.addParam("type", type);
  url.addParam("keywords", keywords);
  url.requestUpdate("list-orders-"+type, { waitingText: null } );
  
  return false;
}

function refreshLists(keywords) {
  if (!window.opener) {
    refreshListOrders("waiting",   keywords);
    refreshListOrders("locked",    keywords);
    refreshListOrders("pending",   keywords);
    refreshListOrders("received",  keywords);
    refreshListOrders("cancelled", keywords);
  } else {
    window.opener.refreshLists();
  }
  return false;
}

function popupOrder(iOrderId, iWidth, iHeight, bAutofill) {
  var width = iWidth?iWidth:500;
  var height = iHeight?iHeight:500;

  var url = new Url();
  url.setModuleAction("dPstock", "vw_aed_order", null, null);
  url.addParam("order_id", iOrderId);
  url.addParam("_autofill", bAutofill != undefined);

  url.pop(width, height, "Edition/visualisation commande");
}

function popupOrderForm(iOrderId, iWidth, iHeight) {
  var width = iWidth?iWidth:500;
  var height = iHeight?iHeight:500;

  var url = new Url();
  url.setModuleAction("dPstock", "vw_order_form", null, null);
  url.addParam("order_id", iOrderId);

  url.pop(width, height, "Bon de commande");
}