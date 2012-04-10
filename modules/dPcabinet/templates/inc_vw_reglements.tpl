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
}
delReglement= function(reglement_id){
  var oForm = getForm('reglement-delete');
  $V(oForm.reglement_id, reglement_id);
  confirmDeletion(oForm);
}
</script>

<fieldset>
  <legend>Règlement</legend>
    {{if $facture->du_patient}}
      <!-- Formulaire de suppression d'un reglement (car pas possible de les imbriquer) -->
      <form name="reglement-delete" action="#" method="post">
        <input type="hidden" name="m" value="dPcabinet" />
        <input type="hidden" name="del" value="1" />
        <input type="hidden" name="dosql" value="do_reglement_aed" />
        <input type="hidden" name="reglement_id" value="" />
      </form>
    
      <script type="text/javascript">Main.add( function() { prepareForm(document.forms["reglement-add"]); } );</script>
      
      <form name="reglement-add" action="" method="post">
        <input type="hidden" name="m" value="dPcabinet" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_reglement_aed" />

        <input type="hidden" name="date" value="now" />
        <input type="hidden" name="emetteur" value="patient" />
        <input type="hidden" name="object_id" value="{{$facture->_id}}" />
        <input type="hidden" name="object_class" value="CFactureConsult" />
      
        <table class="main tbl">
          <tr>
            <th class="category">
              {{mb_label object=$reglement field=mode}}
              ({{mb_label object=$reglement field=banque_id}})
            </th>
            <th class="category" style="width: 6em;">{{mb_label object=$reglement field=montant}}</th>
            <th class="category" style="width: 6em;">{{mb_label object=$reglement field=date}}</th>
            <th class="category" style="width: 0em;"></th>
          </tr>
          
          <!--  Liste des reglements deja effectués -->
          {{foreach from=$facture->_ref_reglements item=_reglement}}
          <tr>
            <td>
              {{mb_value object=$_reglement field=mode}}
              {{if $_reglement->_ref_banque->_id}}
                ({{$_reglement->_ref_banque}})
              {{/if}}
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
          {{if $facture->_du_patient_restant > 0}}
          <tr>
            <td>
              {{mb_field object=$reglement field=mode emptyLabel="Choose" onchange="updateBanque(this)"}}
              {{mb_field object=$reglement field=banque_id options=$banques style="display: none"}}
            </td>
            <td>{{mb_field object=$reglement field=montant}}</td>
            <td></td>
            <td><button class="add notext" type="button" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function(){ Facture.reloadReglement('{{$facture->_id}}')}});">{{tr}}Add{{/tr}}</button></td>
          </tr>
         {{/if}}
          <tr>
            <td colspan="4" style="text-align: center;">
              {{mb_value object=$facture field=_reglements_total_patient}} réglés, 
              <strong>{{mb_value object=$facture field=_du_patient_restant}} restant</strong>
            </td>
          </tr>
        </table>
      </form>
    {{/if}}
</fieldset>