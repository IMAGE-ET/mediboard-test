<script>
updateBanque = function(mode) {
  var banque_id = mode.form.banque_id;
  if ($V(mode) == "cheque") {
    banque_id.show();
  }
  else {
    banque_id.hide();
    $V(banque_id, "");
  }
  var bvr = mode.form.num_bvr;
  if ($V(mode) == "BVR") {
    bvr.show();
  }
  else {
    bvr.hide();
  }
}

delReglement= function(reglement_id){
  var oForm = getForm('reglement-delete');
  $V(oForm.reglement_id, reglement_id);
  confirmDeletion(oForm, { ajax: true, typeName:'le règlement' }, {
     onComplete : function() {
      var url = new Url('dPcabinet', 'ajax_view_facture');
      {{if isset($facture|smarty:nodefaults)}}
        url.addParam('factureconsult_id'    ,'{{$facture->_id}}');
      {{elseif isset($consult|smarty:nodefaults)}}
        url.addParam('consult_id'    ,'{{$consult->_id}}');
      {{/if}}
      url.requestUpdate("load_facture");
    }
  });
}

AddReglement = function (oForm){
  return onSubmitFormAjax(oForm, {
    onComplete : function() {
      var name = "";
      {{if isset($facture|smarty:nodefaults)}}
        name = "factureconsult_id";
      {{elseif isset($consult|smarty:nodefaults)}}
        name = "consult_id";
      {{/if}}
      var url = new Url('dPcabinet'   , 'ajax_view_facture');
      url.addParam(name, oForm.object_id.value);
      url.requestUpdate('load_facture');
    }
  });
}

modifMontantBVR = function (num_bvr){
   var eclat = num_bvr.split('>')[0];
   var montant_bvr = eclat.substring(2, 12)/100;
   var form = getForm("reglement-add");
   form.montant.value = montant_bvr;
}
</script>
{{if $facture->_id}}
  {{assign var=object value=$facture}}
{{else}}
  {{assign var=object value=$consult}}
{{/if}}
<fieldset>
  <legend>Règlements ({{tr}}{{$object->_class}}{{/tr}})</legend>
    {{if $object->du_patient}}
      <!-- Formulaire de suppression d'un reglement (car pas possible de les imbriquer) -->
      <form name="reglement-delete" action="#" method="post">
        <input type="hidden" name="m" value="dPcabinet" />
        <input type="hidden" name="del" value="1" />
        <input type="hidden" name="dosql" value="do_reglement_aed" />
        <input type="hidden" name="reglement_id" value="" />
      </form>
    
      <script type="text/javascript">Main.add( function() { prepareForm(document.forms["reglement-add"]); } );</script>
      
      <form name="reglement-add" action="" method="post" >
        <input type="hidden" name="m" value="{{$m}}" />
        {{*<input type="hidden" name="tab" value="vw_factures" />*}}
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_reglement_aed" />
        <input type="hidden" name="date" value="now" />
        <input type="hidden" name="emetteur" value="patient" />
        <input type="hidden" name="object_id" value="{{$object->_id}}" />
        <input type="hidden" name="object_class" value="{{$object->_class}}" />
        <table class="main tbl">
          <tr>
            <th class="category">
              {{if isset($facture|smarty:nodefaults)}}
                {{mb_include module=system template=inc_object_notes      object=$facture}}
              {{/if}}
              {{mb_label object=$reglement field=mode}}
              ({{mb_label object=$reglement field=banque_id}})
            </th>
            <th class="category" style="width: 6em;">{{mb_label object=$reglement field=montant}}</th>
            <th class="category" style="width: 6em;">{{mb_label object=$reglement field=date}}</th>
            <th class="category" style="width: 0em;"></th>
          </tr>
          
          <!--  Liste des reglements deja effectués -->
          {{foreach from=$object->_ref_reglements item=_reglement}}
          <tr>
            <td>
              {{mb_value object=$_reglement field=mode}}
              {{if $_reglement->_ref_banque->_id}}
                ({{$_reglement->_ref_banque}})
              {{/if}}
              {{if $_reglement->num_bvr}}( {{$_reglement->num_bvr}} ){{/if}}
            </td>
            <td>{{mb_value object=$_reglement field=montant}}</td>
            <td>
              <label title="{{mb_value object=$_reglement field=date}}">
                {{$_reglement->date|date_format:$conf.date}}
              </label>
            </td>
            <td>
              <button class="remove notext" type="button" onclick="delReglement('{{$_reglement->reglement_id}}');"></button>
            </td>
          </tr>
          {{/foreach}}
          {{if ($object->_du_patient_restant) > 0}}
            <tr>
              <td>
                {{mb_field object=$reglement field=mode emptyLabel="Choose" onchange="updateBanque(this)"}}
                {{mb_field object=$reglement field=banque_id options=$banques style="display: none"}}
                {{if isset($object->_num_bvr|smarty:nodefaults)}}
                  <select name="num_bvr" style="display:none;" onchange="modifMontantBVR(this.value);" >
                    <option value="0">&mdash; Choisir un numéro</option>
                    {{foreach from=$object->_num_bvr item=num}}
                      <option value="{{$num}}">
                        {{$num}}
                      </option>
                    {{/foreach}}
                  </select>
                {{/if}}
              </td>
              <td><input type="text" class="currency notNul" size="4" maxlength="8" name="montant" value="{{$object->_du_patient_restant}}" /></td>
              <td></td>
              <td><button class="add notext" type="button" onclick="AddReglement(this.form);">{{tr}}Add{{/tr}}</button></td>
            </tr>
          {{/if}}
          <tr>
            <td colspan="4" style="text-align: center;">
              {{mb_value object=$object field=_reglements_total_patient}} réglés, 
              <strong>{{mb_value object=$object field=_du_patient_restant}} restant</strong>
            </td>
          </tr>

          {{if $object->patient_date_reglement}}
          <tr>
            <td colspan="4" style="text-align: center;">
              <strong>
                {{mb_label object=$object field=patient_date_reglement}}
                le 
                {{mb_value object=$object field=patient_date_reglement}}
              </strong>
            </td>
          </tr>
          {{/if}}

        </table>
      </form>
    {{/if}}
</fieldset>