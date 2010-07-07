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

{{mb_include_script module=dPstock script=barcode ajax=true}}

<script type="text/javascript">
  
Barcode.code128Prefixes = {{"CDMI"|static:code128_prefixes|@json}};

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

search_product_order_item_reception = function(form, onComplete){
  var url = new Url("dmi", "httpreq_do_search_product_order_item_reception");
  url.addFormData(form);
  url.requestUpdate("list_product_order_item_reception", {onComplete: onComplete || function(){} });
  return false;
}

Main.add(function () {
  Prescription.refreshTabHeader("div_dmi", {{$prescription->_ref_lines_dmi|@count}});
  
  var formDmiDelivery = getForm("dmi_delivery_by_product");
  var searchField = formDmiDelivery._view;
  
  var url = new Url("dmi", "httpreq_do_product_autocomplete");
  var autocompleter = url.autoComplete(searchField, formDmiDelivery._view.id+'_autocomplete', {
    minChars: 2,
    //frequency: 1.5,
    updateElement : function(element) {
      var id = element.id;
      var className = element.up().className;
      var lot_number    = className.match(/lot_number\|([^ ]*)/);
      var scc_code_part = className.match(/scc_code_part\|([^ ]*)/);
      var lapsing_date  = className.match(/lapsing_date\|([^ ]*)/);
      
      if (lot_number) lot_number = lot_number[1];
      if (scc_code_part) scc_code_part = scc_code_part[1];
      if (lapsing_date) lapsing_date = lapsing_date[1];
      
      $V(formDmiDelivery.product_id, (id ? id.split('-')[1] : ""));
      $V(formDmiDelivery._lot_number, lot_number);
      $V(formDmiDelivery._scc_code_part, scc_code_part);
      $V(formDmiDelivery._lapsing_date, lapsing_date);
      
      var onComplete = (lot_number ? function(){
        var field = getForm("searchProductOrderItemReception")._search_lot;
        $V(field, lot_number);
        
        (function(lot_number, field){
          filterByLotNumber(null, lot_number);
          selectAvailableLine(null, field);
        }).delay(0.1, lot_number, field); // argh
        
      } : Prototype.emptyFunction);
      
      search_product_order_item_reception(formDmiDelivery, onComplete);
      
      if (element.enterKeyPressed) {
        $V(searchField, "" /*element.down(".view").innerHTML.stripTags().strip()*/);
      }
    },
    onAfterShow: function(element, update){
      if (element.enterKeyPressed) {
        element.select();
        if (update.select("li").length == 1) {
          update.hide();
          autocompleter.selectEntry(0);
        }
      }
    },
    //dropdown: true,
    //autoSelect: true,
    callback: function(input, queryString){
      return (queryString + "&category_id={{$dPconfig.dmi.CDMI.product_category_id}}"); 
    }
  });
  
  searchField.goodCharsCount = 0;
  //searchField.stopObserving("click").stopObserving("focus"); // used when dropdown: true
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
    
    trigger.enterKeyPressed = (charCode == 13);
    
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
  
  dmiAutocompleter = autocompleter;
});

filterByLotNumber = function(e, term) {
  var table = $("lot-list");
  table.select("tr").invoke("show");
  
  term = term || $V(Event.element(e));
  if (!term) return;
  
  table.select(".CProductOrderItemReception-view").each(function(e) {
    if (!e.innerHTML.like(term)) {
      e.up("tr").hide();
    }
  });
}

selectAvailableLine = function(e, element){
  var visible = $("lot-list").select(".CProductOrderItemReception-view").filter(function(e){
    return e.up("tr").visible();
  });
  
  $("lot-list").select("tr").invoke("removeClassName", "selected");

  if (visible.length) {
    var tr = visible[0].up("tr");
    tr.down("button").focus();
  }
  else (element || Event.element(e)).select();
}

reloadListDMI = function(){
  Prescription.reload('{{$prescription->_id}}', null, "dmi");
}

addDMI = function(product_id, order_item_reception_id, septic, type, quantity){
  var oFormDMI = getForm("add_dmi");
  
  // si la liste des praticien est affichée, on utilise le praticien selectionné
  if(document.selPraticienLine) {
    oFormDMI.praticien_id.value = document.selPraticienLine.praticien_id.value;
  }
  oFormDMI.product_id.value = product_id;
  oFormDMI.order_item_reception_id.value = order_item_reception_id;
  oFormDMI.septic.value = septic;
  oFormDMI.elements.type.value = type;
  oFormDMI.quantity.value = quantity;
  return onSubmitFormAjax(oFormDMI, { onComplete: reloadListDMI } );
}

delLineDMI = function(line_dmi_id){
  var oFormDMI = getForm("del_dmi");
  oFormDMI.prescription_line_dmi_id.value = line_dmi_id;
  return onSubmitFormAjax(oFormDMI, { onComplete: reloadListDMI } );
}

signLineDMI = function(line_dmi_id, value){
  var oFormDMI = getForm("sign_dmi");
  oFormDMI.prescription_line_dmi_id.value = line_dmi_id;
  oFormDMI.signed.value = value;
  return onSubmitFormAjax(oFormDMI, { onComplete: reloadListDMI } );
}
</script>

<form name="add_dmi" method="post" action="">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_line_dmi_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_line_dmi_id" value="" />
  <input type="hidden" name="product_id" value="" />
  <input type="hidden" name="order_item_reception_id" value="" />
  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
  <input type="hidden" name="operation_id" value="{{$operation_id}}" />
  <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
  <input type="hidden" name="date" value="now" />
  <input type="hidden" name="septic" value="" />
  <input type="hidden" name="type" value="" />
  <input type="hidden" name="quantity" value="" />
  <input type="hidden" name="signed" value="0" />
</form>

<form name="del_dmi" method="post" action="">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_line_dmi_aed" />
  <input type="hidden" name="del" value="1" />
  <input type="hidden" name="prescription_line_dmi_id" value="" />
</form>

<form name="sign_dmi" method="post" action="">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_line_dmi_aed" />
  <input type="hidden" name="signed" value="" />
  <input type="hidden" name="prescription_line_dmi_id" value="" />
</form>

<div class="small-warning" id="capslock-alert" style="display: none;">
  Il semble que la touche <strong>Verr. Majuscules</strong> de votre clavier est activée, <br/>
  veuillez la désactiver pour permettre une bonne lecture du code barre.
</div>
              
<table class="form">
  {{*  
  <tr>
    <th colspan="2" class="category">Recherche par produit</th>
  </tr>
  *}}
  <tr>
    <th>Produit / Code barre</th>
    <td>
      <form name="dmi_delivery_by_product" method="get" action="" onsubmit="return false">
        <input type="hidden" name="product_id" value="" />
        <input type="hidden" name="_scc_code_part" value="" />
        <input type="hidden" name="_lot_number" value="" />
        <input type="hidden" name="_lapsing_date" value="" />
        <input type="text" name="_view" size="40" value="" style="font-size: 1.3em;" />
        <div id="dmi_delivery__view_autocomplete" style="display: none; width: 350px;" class="autocomplete"></div>
      </form>
    </td>
  </tr>
</table>

<div id="list_product_order_item_reception"></div>

{{if $prescription->_ref_lines_dmi|@count}}
<table class="tbl">
  <!-- Affichage des lignes de DMI-->
  <tr>
    <th style="width: 16px;"></th>
    <th>{{mb_title class=CPrescriptionLineDMI field=product_id}}</th>
    <th>{{mb_title class=CPrescriptionLineDMI field=quantity}}</th>
    <th>{{mb_title class=CPrescriptionLineDMI field=type}}</th>
    <th>{{mb_title class=CPrescriptionLineDMI field=septic}}</th>
    <th>{{mb_title class=CPrescriptionLineDMI field=date}}</th>
    <th>Code produit</th>
    <th>Code lot</th>
    <th style="width: 1%">Praticien</th>
    <th style="width: 1%">Sign.</th>
  </tr>
  {{foreach from=$prescription->_ref_lines_dmi item=_line_dmi}}
    <tr>
      <td>
        {{if !$_line_dmi->signed}}
          <button type="button" class="trash notext" onclick="delLineDMI('{{$_line_dmi->_id}}');"></button>
        {{/if}}
      </td>
      <td>
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
      <td style="text-align: center;">
        {{if $_line_dmi->_can_view_form_signature_praticien}}
          {{if !$_line_dmi->signed}}
            <button type="button" class="tick notext" onclick="signLineDMI('{{$_line_dmi->_id}}', 1);">Signer</button>
          {{else}}
            <button type="button" class="cancel notext" onclick="signLineDMI('{{$_line_dmi->_id}}', 0);">Annuler la signature</button>
          {{/if}}
        {{else}}
          {{if $_line_dmi->signed}}
            <img src="images/icons/tick.png" title="Signée par le praticien" />
          {{/if}}
        {{/if}}
      </td>
    </tr>
  {{/foreach}}
</table>

{{else}}
  <div class="small-info">Il n'y a aucun DMI dans cette prescription</div>
{{/if}}