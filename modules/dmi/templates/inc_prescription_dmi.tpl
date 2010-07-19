<script type="text/javascript">
DMI_prescription_id = '{{$prescription->_id}}';
DMI_operation_id = '{{$operation_id}}';
  
function parseBarcode(barcode) {
  var url = new Url("dmi", "httpreq_parse_barcode");
  url.addParam("barcode", barcode);
  url.requestUpdate("parsed-barcode");
}

reloadListDMI = function(){
  Prescription.reload('{{$prescription->_id}}', null, "dmi");
}

submitBarcode = function(){
  var barcode = $("barcode");
  parseBarcode(barcode.value);
  barcode.select();
}

Main.add(function(){
  var barcode = $("barcode");
  
  barcode.goodCharsCount = 0;
  
  barcode.observe("keypress", function(e){
    var input = Event.element(e);
    var charCode = Event.key(e);
    
    input.enterKeyPressed = (charCode == 13);
    
    // Checks if Caps Lock is activated
    if (Event.isCapsLock(e) || "&é\"'èçà".include(String.fromCharCode(charCode))) {
      if (input.oTooltip)
        input.oTooltip.show();
      else 
        ObjectTooltip.createDOM(this, 'capslock-alert', {duration: 0}); 
        
      input.goodCharsCount = 0;
    }
    else {
      input.goodCharsCount++;
      if(input.oTooltip && input.goodCharsCount > 4) {
        input.oTooltip.hide();
      }
    }

    if (input.enterKeyPressed && input.value.length > 2) {
      parseBarcode(input.value);
      input.select();
    }
  });
  
  barcode.focus();
});
</script>

<div class="small-warning" id="capslock-alert" style="display: none;">
  Il semble que la touche <strong>Verr. Majuscules</strong> de votre clavier est activée, <br/>
  veuillez la désactiver pour permettre une bonne lecture du code barre.
</div>

<div style="text-align: center; padding: 0.3em;">
  Produit / Code barre
  <input type="text" id="barcode" size="50" class="barcode" style="font-size: 1.3em;" autocomplete="off" placeholder="Code barre" />
  <hr />
</div>

<div id="parsed-barcode"></div>

{{if @$prescription->_ref_lines_dmi|@count}}
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
          <form name="del-dmi-{{$_line_dmi->_id}}" method="post" action="" onsubmit="return onSubmitFormAjax(this, {onComplete: reloadListDMI})">
            <input type="hidden" name="m" value="dPprescription" />
            <input type="hidden" name="dosql" value="do_prescription_line_dmi_aed" />
            <input type="hidden" name="del" value="1" />
            <input type="hidden" name="prescription_line_dmi_id" value="{{$_line_dmi->_id}}" />
            <button type="submit" class="trash notext"></button>
          </form>
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
        
          <form name="sign-dmi-{{$_line_dmi->_id}}" method="post" action="" onsubmit="return onSubmitFormAjax(this, {onComplete: reloadListDMI})">
            <input type="hidden" name="m" value="dPprescription" />
            <input type="hidden" name="dosql" value="do_prescription_line_dmi_aed" />
            <input type="hidden" name="prescription_line_dmi_id" value="{{$_line_dmi->_id}}" />
          
            {{if !$_line_dmi->signed}}
              <input type="hidden" name="signed" value="1" />
              <button type="submit" class="tick notext">Signer</button>
            {{else}}
              <input type="hidden" name="signed" value="0" />
              <button type="submit" class="cancel notext">Annuler la signature</button>
            {{/if}}
          </form>
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
