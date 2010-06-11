{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

barcodeSeparator = " ";

check_separator = function(element, next_element){
  if(element.value.charAt(element.value.length-1) == barcodeSeparator){
    $(next_element).focus();
    element.value = element.value.substring(0, element.value.length-1);
    return false;
  }
  return true;
}

search_product = function(form){
  var url = new Url("dmi", "httpreq_do_search_product");
  url.addParam("code", form.code.value);
  url.addParam("code_lot", form.code_lot.value);
  url.requestUpdate("product_description_code");
  return false;
}

search_product_code = function(code, code_lot) {
  var url = new Url("dmi", "httpreq_do_search_product");
  url.addParam("code", code);
  url.addParam("code_lot", code_lot);
  url.requestUpdate("product_reception_by_product");
  return false;
}

search_product_order_item_reception = function(form){
  var url = new Url("dmi", "httpreq_do_search_product_order_item_reception");
  url.addParam("product_id", form.product_id.value);
  url.requestUpdate("list_product_order_item_reception");
  return false;
}

Main.add(function () {
  var formDmiDelivery = getForm("dmi_delivery_by_product");
  url = new Url("dmi", "httpreq_do_product_autocomplete");
  url.autoComplete(formDmiDelivery._view, formDmiDelivery._view.id+'_autocomplete', {
    minChars: 2,
    updateElement : function(element) {
      $V(formDmiDelivery.product_id, element.id.split('-')[1]);
      $V(formDmiDelivery._view, element.down(".view").innerHTML.stripTags().strip());
      search_product_order_item_reception(formDmiDelivery);
    },
    dropdown: true,
    callback: function(input, queryString){
      return (queryString + "&category_id={{$dPconfig.dmi.CDMI.product_category_id}}"); 
    }
  });
  
  Prescription.refreshTabHeader("div_dmi", {{$prescription->_ref_lines_dmi|@count}});
});

reloadListDMI = function(){
  Prescription.reload('{{$prescription->_id}}', null, "dmi");
}

addDMI = function(product_id, order_item_reception_id, septic, type, quantity){
  var oFormAddDMI = getForm("add_dmi");
  
  // si la liste des praticien est affichée, on utilise le praticien selectionné
  if(document.selPraticienLine) {
    oFormAddDMI.praticien_id.value = document.selPraticienLine.praticien_id.value;
  }
  oFormAddDMI.product_id.value = product_id;
  oFormAddDMI.order_item_reception_id.value = order_item_reception_id;
  oFormAddDMI.septic.value = septic;
  oFormAddDMI.elements.type.value = type;
  oFormAddDMI.quantity.value = quantity;
  return onSubmitFormAjax(oFormAddDMI, { onComplete: reloadListDMI } );
}

delLineDMI = function(line_dmi_id){
  var oFormAddDMI = getForm("del_dmi");
  oFormAddDMI.prescription_line_dmi_id.value = line_dmi_id;
  return onSubmitFormAjax(oFormAddDMI, { onComplete: reloadListDMI } );
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
  <input type="hidden" name="operation_id" value="{{$operation_id}}">
  <input type="hidden" name="praticien_id" value="{{$app->user_id}}">
  <input type="hidden" name="date" value="now">
  <input type="hidden" name="septic" value="">
  <input type="hidden" name="type" value="">
  <input type="hidden" name="quantity" value="">
</form>

<form name="del_dmi" method="post" action="">
  <input type="hidden" name="m" value="dPprescription">
  <input type="hidden" name="dosql" value="do_prescription_line_dmi_aed">
  <input type="hidden" name="del" value="1" />
  <input type="hidden" name="prescription_line_dmi_id" value="" />
</form>

<table class="main" style="table-layout: fixed;">
  <tr>
    <td>
      
      <form name="dmi_delivery_by_code" method="get" action="" onsubmit="return search_product(this)">
        <input type="hidden" name="product_id" value="" />
        
	      <table class="form">
	        <tr>
	          <th colspan="2" class="category">Recherche par code barre</th>
	        </tr>
	        <tr>
	          <th>
	            <label for="code">Code produit</label> / 
              <label for="code_lot">Code lot</label>
            </th>
	          <td>
	            <input type="text" size="10" name="code" onkeyup="return check_separator(this,this.form.code_lot)"/>
              <input type="text" size="10" name="code_lot" />
              <button type="submit" class="search notext">{{tr}}Search{{/tr}}</button>
            </td>
	        </tr>
	        <tr>
	          <td id="product_description_code" colspan="2"></td>
	        </tr>
	      </table>
      
      </form>
      
    </td>
    <td>
      <table class="form">
        <tr>
          <th colspan="2" class="category">Recherche par produit</th>
        </tr>
        <tr>
          <th>Produit</th>
          <td>
            <form name="dmi_delivery_by_product" method="get" action="" onsubmit="return false">
              <input type="hidden" name="product_id" value="" />
	            <input type="text" name="_view" size="25" value="" />
	            <div id="dmi_delivery__view_autocomplete" style="display: none; width: 300px;" class="autocomplete"></div>
	          </form>
          </td>
        </tr>
        <tr>
          <td id="list_product_order_item_reception" colspan="2"></td>
        </tr>
      </table>
    </td>
  </tr>
</table>

{{if $prescription->_ref_lines_dmi|@count}}
<table class="tbl">
  <!-- Affichage des lignes de DMI-->
  <tr>
    <th>{{mb_title class=CPrescriptionLineDMI field=product_id}}</th>
    <th>{{mb_title class=CPrescriptionLineDMI field=quantity}}</th>
    <th>{{mb_title class=CPrescriptionLineDMI field=type}}</th>
    <th>{{mb_title class=CPrescriptionLineDMI field=septic}}</th>
    <th>{{mb_title class=CPrescriptionLineDMI field=date}}</th>
    <th>Code produit</th>
    <th>Code lot</th>
    <th style="width: 1%">Praticien</th>
  </tr>
  {{foreach from=$prescription->_ref_lines_dmi item=_line_dmi}}
    <tr>
      <td>
        <button type="button" class="trash notext" onclick="delLineDMI('{{$_line_dmi->_id}}');"></button>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_line_dmi->_ref_product->_guid}}')">
          {{$_line_dmi->_ref_product}}
        </span>
      </td>
      <td>{{mb_value object=$_line_dmi field=quantity}}</td>
      <td>{{mb_value object=$_line_dmi field=type}}</td>
      <td {{if $_line_dmi->septic}}class="cancelled"{{/if}}>
        {{mb_value object=$_line_dmi field=septic}}
      </td>
      <td>{{mb_value object=$_line_dmi field=date}}</td>
      <td>{{mb_value object=$_line_dmi->_ref_product field=code}}</td>
      <td>{{mb_value object=$_line_dmi->_ref_product_order_item_reception field=code}}</td>
      <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_line_dmi->_ref_praticien}}</td>
    </tr>
  {{/foreach}}
</table>

{{else}}
  <div class="small-info">Il n'y a aucun DMI dans cette prescription</div>
{{/if}}