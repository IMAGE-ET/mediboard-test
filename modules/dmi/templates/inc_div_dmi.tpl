{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{* 

Algo de lecture de code barre :

Si code barre complet trouvé dans base MB
  Produit OK
  Lot     OK
Sinon, Si seulement code produit trouvé
  Lecture produit
  Listage lots du produit
  Affichage d'un champ de saisie de code lot
  Si code lot trouvé
    Séléction du lot
  Sinon
    "Lot non retrouvé"
  FinSi
FinSi

*}}

{{mb_include_script module=dmi script=dmi ajax=true}}

<script type="text/javascript">

search_product = function(form){
  var url = new Url("dmi", "httpreq_do_search_product");
  url.addParam("code", form.code.value);
  url.requestUpdate("product_description_code", {onComplete: function(){form.code.select()}});
  return false;
}

search_product_code = function(code) {
  var url = new Url("dmi", "httpreq_do_search_product");
  url.addParam("code", code);
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
  Prescription.refreshTabHeader("div_dmi", {{$prescription->_ref_lines_dmi|@count}});
  
  var formDmiDelivery = getForm("dmi_delivery_by_product");
  var searchField = formDmiDelivery._view;
  
  var url = new Url("dmi", "httpreq_do_product_autocomplete");
  var autocompleter = url.autoComplete(searchField, formDmiDelivery._view.id+'_autocomplete', {
    minChars: 2,
    updateElement : function(element) {
      var id = element.id;
      $V(searchField, "" /*element.down(".view").innerHTML.stripTags().strip()*/);
      $V(formDmiDelivery.product_id, (id ? id.split('-')[1] : ""));
      search_product_order_item_reception(formDmiDelivery);
    },
    dropdown: true,
    autoSelect: true,
    callback: function(input, queryString){
      input.select();
      return (queryString + "&category_id={{$dPconfig.dmi.CDMI.product_category_id}}"); 
    }
  });
  
  searchField.goodCharsCount = 0;
  searchField.stopObserving("click").stopObserving("focus");
  searchField.focus();
  
  /*searchField.observe("keypress", function(e){
    var enterKey = Event.key(e) == 13;
    autocompleter._selectElement = enterKey;
    if (enterKey)
      autocompleter.element.select();
  });*/
  
  // Checks if Caps Lock is activated
  searchField.observe("keypress", function(e){
    var trigger = Event.element(e);
    var charCode = Event.key(e);
    
    if (Event.isCapsLock(e) || "&é\"'èçà".include(String.fromCharCode(charCode))) {
      if (trigger.oTooltip)
        trigger.oTooltip.show();
      else 
        ObjectTooltip.createDOM(this, 'capslock-alert', {duration: 0}); 
        
      trigger.goodCharsCount = 0;
    }
    else {
      trigger.goodCharsCount++;
      if(trigger.oTooltip && trigger.goodCharsCount > 4) {
        trigger.oTooltip.hide();
      }
    }
  });
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

<div class="small-warning" id="capslock-alert" style="display: none;">
  Il semble que la touche <strong>Verr. Majuscules</strong> de votre clavier est activée, <br/>
  veuillez la désactiver pour permettre une bonne lecture du code barre.
</div>
              
<table class="main" style="table-layout: fixed;">
  <tr>
    
    {{* 
    <td>
      <form name="dmi_delivery_by_code" method="get" action="" onsubmit="return search_product(this)">
        <input type="hidden" name="product_id" value="" />
        
	      <table class="form">
	        <tr>
	          <th colspan="2" class="category">Recherche par code barre</th>
	        </tr>
	        <tr>
	          <th>
	            <label for="code">Code barre</label>
            </th>
	          <td>
	            <input type="text" size="30" name="code" />
              <button type="submit" class="search notext">{{tr}}Search{{/tr}}</button>
            </td>
	        </tr>
	        <tr>
	          <td id="product_description_code" colspan="2" class="text"></td>
	        </tr>
	      </table>
      
      </form>
    </td>
    *}}
    
    <td>
      <table class="form">
        {{*  
        <tr>
          <th colspan="2" class="category">Recherche par produit</th>
        </tr>
        *}}
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
      </table>
    </td>
  </tr>
</table>

<div id="list_product_order_item_reception"></div>

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