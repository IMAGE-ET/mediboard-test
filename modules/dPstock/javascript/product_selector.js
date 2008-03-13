// $Id: $

var ProductSelector = {
  sForm       : null,
  sId         : null,
  sView       : null,
  options : {
    width : 700,
    height: 400
  },

  pop: function(product_id) {
    var oForm = document[this.sForm];
    var url = new Url();
    url.setModuleAction("dPstock", "product_selector");
    url.addParam("product_id", product_id);
    url.popup(this.options.width, this.options.height, "Sélecteur de produit");
  },

  set: function(product_id, product_name) {
    var oForm = document[this.sForm];
    Form.Element.setValue(oForm[this.sId] , product_id);
    Form.Element.setValue(oForm[this.sView] , product_name);
  }
}