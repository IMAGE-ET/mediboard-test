{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

check_separator = function(element, next_element){
  if(element.value.charAt(element.value.length-1)==' '){
    $(next_element).focus();
    element.value = element.value.substring(0,element.value.length-1);
    return false;
  }
  return true;
}

search_product = function(form){
  var url = new Url;
  url.setModuleAction("dmi", "httpreq_do_search_product");
  url.addParam("code", form.code.value);
  url.addParam("code_lot", form.code_lot.value);
  url.requestUpdate("product_description");
  return false;
}

search_product_order_item_reception = function(){
  var form = getForm("dmi_delivery"); 
  var url = new Url;
  url.setModuleAction("dmi", "httpreq_do_search_product_order_item_reception");
  url.addParam("product_id", form.product_id.value);
  url.requestUpdate("list_product_order_item_reception");
  return false;
}

Main.add(function () {
  var formDmiDelivery = getForm("dmi_delivery");
  urlProduct = new Url();
  urlProduct.setModuleAction("dPstock", "httpreq_do_product_autocomplete");
  urlProduct.autoComplete(formDmiDelivery._view, formDmiDelivery._view.id+'_autocomplete', {
    minChars: 2,
    updateElement : function(element) {
      $V(formDmiDelivery.product_id, element.id.split('-')[1]);
      $V(formDmiDelivery._view, element.select(".view")[0].innerHTML);
      search_product_order_item_reception();
    },
    callback: function(input, queryString){
      return (queryString + "&category_id={{$dPconfig.dmi.CDMI.product_category_id}}"); 
    }
  });
});

reloadListDMI = function(){
  Prescription.reload('{{$prescription->_id}}', null, "dmi");
}

addDMI = function(product_id, order_item_reception_id){
  oFormAddDMI = document.add_dmi;
  // si la liste des praticien est affichée, on utilise le praticien selectionné
  if(document.selPraticienLine) {
    oFormAddDMI.praticien_id.value = document.selPraticienLine.praticien_id.value;
  }
  oFormAddDMI.product_id.value = product_id;
  oFormAddDMI.order_item_reception_id.value = order_item_reception_id;
  submitFormAjax(oFormAddDMI, "systemMsg", { onComplete: function(){
    reloadListDMI();
  } } );
}

delLineDMI = function(line_dmi_id){
  oFormAddDMI = document.add_dmi;
  oFormAddDMI.prescription_line_dmi_id.value = line_dmi_id;
  oFormAddDMI.del.value = "1";
  submitFormAjax(oFormAddDMI, "systemMsg", { onComplete: function(){
    reloadListDMI();
  } } );
}
</script>

<form name="add_dmi" method="post" action="">
  <input type="hidden" name="m" value="dPprescription">
  <input type="hidden" name="dosql" value="do_prescription_line_dmi_aed">
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_line_dmi_id" value="" />
  <input type="hidden" name="product_id" value="">
  <input type="hidden" name="order_item_reception_id" value="">
  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}">
  <input type="hidden" name="praticien_id" value="{{$app->user_id}}">
</form>

<form name="dmi_delivery" method="post" action="" onsubmit="return search_product(this)">
  <input type="hidden" name="product_id" value="" />
	<table class="main">
	  <tr>  
	    <td>
	      <table class="form">
	        <tr>
	          <th colspan="10" class="title">Recherche par code barre</th>
	        </tr>
	        <tr>
	          <th>Code produit</th>
	          <td><input type="text" name="code" onkeyup="return check_separator(this,this.form.code_lot)"/></td>
	        </tr>
	        <tr>
	          <th>Code lot</th>
	          <td><input type="text" name="code_lot" /></td>
	        </tr>
	        <tr>
	          <td colspan="2" style="text-align: center">
	            <button type="button" class="search" onclick="this.form.onsubmit();">Rechercher</button>
	          </td>
	        </tr>
	        <tr>
	          <th></th>
	          <td id="product_description" colspan="10"></td>
	        </tr>
	      </table>
	    </td>
	    <td>
	      <table class="form">
	        <tr>
	          <th colspan="10" class="title">Recherche par produit</th>
	        </tr>
	        <tr>
	          <th>Produit</th>
	          <td>
	            <input type="text" name="_view" size="30" value="" />
	            <div id="dmi_delivery__view_autocomplete" style="display: none; width: 300px;" class="autocomplete"></div>
	          </td>
	        </tr>
	        <tr>
	          <th>Lot(s)</th>
	          <td id="list_product_order_item_reception"></td>
	        </tr>
	      </table>
	    </td>
	  </tr>
	</table>
</form>

<table class="tbl">
  <!-- Affichage des lignes de DMI-->
  <tr>
    <th>Produit</th>
    <th>Code produit</th>
    <th>Code lot</th>
  </tr>
	{{foreach from=$prescription->_ref_lines_dmi item=_line_dmi}}
	  <tr>
	    <td>
	      <button type="button" class="trash notext" onclick="delLineDMI('{{$_line_dmi->_id}}');"></button>
	      {{$_line_dmi->_ref_product->name}}
	    </td>
	    <td>
	      {{$_line_dmi->_ref_product->code}}
	    </td>
	    <td>
	    {{$_line_dmi->_ref_product_order_item_reception->code}}
	    </td>
	  </tr>
	{{/foreach}}
</table>