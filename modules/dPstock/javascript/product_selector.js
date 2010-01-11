/* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
var ProductSelector = {
  sForm       : null,
  sId         : null,
  sView       : null,
  sQuantity   : null,
  sPackaging  : null,
  sUnit       : null,
  options : {
    width : 800,
    height: 450
  },

  pop: function(product_id) {
    var url = new Url("dPstock", "product_selector");
    url.addParam("product_id", product_id);
    url.popup(this.options.width, this.options.height, "Sélecteur de produit");
  },

  set: function(product_id, product_name, quantity, unit, packaging) {
    var oForm = document[this.sForm];
    $V(oForm[this.sId],        product_id, true);
    $V(oForm[this.sView],      product_name, true);
    $V(oForm[this.sUnit],      unit, true);
    $V(oForm[this.sPackaging], packaging, true);
    $V(oForm[this.sQuantity],  quantity, true);
  }
};