<script>
  updateBanque = function(mode) {
    var form = getForm('reglement-add');
    var banque_id = form.banque_id;
    var reference = form.reference;
    var BVR       = form.num_bvr;
    var mode      = form.mode.value;
    
    banque_id.hide();
    reference.hide();
    BVR.hide();

    {{if !$conf.dPfacturation.CReglement.use_debiteur}}
      var tireur    = form.tireur;
      tireur.hide();
      if (mode == "cheque") {
        tireur.show();
      }
    {{/if}}
    
    switch(mode) {
      case "cheque":
        banque_id.show();
        reference.show();
        break;
      case "virement":
        reference.show();
        $V(banque_id, "");
        break;
        
      case "BVR":
        BVR.show();
        $V(banque_id, "");
        break;
      default:
        $V(banque_id, "");
    }
  }
  updateDebiteur = function(debiteur_id) {
    var url = new Url('dPfacturation', 'ajax_edit_debiteur');
    url.addParam('debiteur_id'   , debiteur_id);
    url.addParam('debiteur_desc' , 1);
    url.requestUpdate("reload_debiteur_desc");
  }

  delReglement = function(reglement_id, facture_class, facture_id){
    var oForm = getForm('reglement-delete');
    $V(oForm.reglement_id, reglement_id);

    return confirmDeletion(oForm, { ajax: true, typeName:'le r�glement' }, {
      onComplete : function() {
        if ($('a_reglements_consult')) {
          Reglement.reload(true);
        }
        if (!$('load_facture')) {
          Facture.url.refreshModal();
        }
        else {
          Facture.reloadReglement(facture_id, facture_class);
          var url = new Url('dPfacturation', 'ajax_view_facture');
          url.addParam('facture_id'   , facture_id);
          url.addParam('facture_class', facture_class);
          url.requestUpdate("load_facture");
        }
      }
    });
  }

  editReglementDate = function(reglement_id, date){
    var oForm = getForm('reglement-edit-date');
    $V(oForm.reglement_id, reglement_id);
    $V(oForm.date,         date);
    
    return onSubmitFormAjax(oForm, function() {
      {{if isset($consult|smarty:nodefaults)}}
      Reglement.reload(true);
      {{/if}}
      Facture.reloadReglement('{{$facture->_id}}', '{{$facture->_class}}');
    });
  }
  
  editAquittementDate = function(date){
    var oForm = getForm('edit-date-aquittement');
    $V(oForm.patient_date_reglement,     date);
    
    return onSubmitFormAjax(oForm, function() {
      {{if isset($consult|smarty:nodefaults)}}
      Reglement.reload(true);
      {{/if}}
      Facture.reloadReglement('{{$facture->_id}}', '{{$facture->_class}}');
    });
  }

  addReglement = function (form){
    return onSubmitFormAjax(form, function() {
      if ($('a_reglements_consult')) {
        Reglement.reload(true);
      }
      else {
        Facture.reloadReglement($V(form.object_id), $V(form.object_class));
      }
    });
  }

  modifMontantBVR = function (num_bvr){
    var eclat = num_bvr.split('>')[0];
    var form = getForm("reglement-add");
    form.montant.value = eclat.substring(2, 12)/100;
  }
  Main.add(function(){
    {{if ($object->_du_restant_patient) > 0 || $conf.dPfacturation.CReglement.use_lock_acquittement}}
    updateBanque('{{$conf.dPfacturation.CReglement.use_mode_default}}');
    {{/if}}
  });
</script>
    
<!-- Formulaire de suppression d'un reglement (car pas possible de les imbriquer) -->
<form name="reglement-delete" action="#" method="post">
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="del" value="1" />
  <input type="hidden" name="dosql" value="do_reglement_aed" />
  <input type="hidden" name="reglement_id" value="" />
</form>

<form name="reglement-edit-date" action="#" method="post">
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_reglement_aed" />
  <input type="hidden" name="reglement_id" value="" />
  <input type="hidden" name="date" value="" />
</form>

<form name="edit-date-aquittement" action="#" method="post">
  {{mb_class object=$object}}
  {{mb_key   object=$object}}
  <input type="hidden" name="patient_date_reglement" value="" />
</form>

<form name="reglement-add" action="" method="post" onsubmit="return addReglement(this);">
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_reglement_aed" />
  <input type="hidden" name="emetteur" value="patient" />
  <input type="hidden" name="object_id" value="{{$object->_id}}" />
  <input type="hidden" name="object_class" value="{{$object->_class}}" />
  <table class="main tbl">
    <tr>
      <th class="category" style="width: 50%;">
        {{if isset($facture|smarty:nodefaults)}}
          {{mb_include module=system template=inc_object_notes object=$facture}}
        {{/if}}
        {{mb_label class=CReglement field=mode}}
        ({{mb_label class=CReglement field=banque_id}})
      </th>
      <th class="category">{{mb_label class=CReglement field=reference}}</th>
      {{if $conf.dPfacturation.CReglement.use_debiteur}}
        <th class="category narrow">{{mb_label class=CReglement field=debiteur_id}}</th>
        <th class="category narrow">{{mb_label class=CReglement field=debiteur_desc}}</th>
      {{else}}
        <th class="category">{{mb_label class=CReglement field=tireur}}</th>
      {{/if}}
      <th class="category narrow">{{mb_label class=CReglement field=montant}}</th>
      <th class="category narrow">{{mb_label class=CReglement field=date}}</th>
      <th class="category narrow"></th>
    </tr>
    
    <!--  Liste des reglements deja effectu�s -->
    {{foreach from=$object->_ref_reglements item=_reglement}}
    <tr>
      <td>
        {{mb_value object=$_reglement field=mode}}
        {{if $_reglement->_ref_banque->_id}}
          ({{$_reglement->_ref_banque}})
        {{/if}}
        {{if $_reglement->num_bvr}}( {{$_reglement->num_bvr}} ){{/if}}
      </td>
      <td>{{mb_value object=$_reglement field=reference}}</td>
      {{if $conf.dPfacturation.CReglement.use_debiteur}}
        <td>{{$_reglement->_ref_debiteur}}</td>
        <td>{{mb_value object=$_reglement field=debiteur_desc}}</td>
      {{else}}
        <td>{{mb_value object=$_reglement field=tireur}}</td>
      {{/if}}
      <td style="text-align: right;">
        {{mb_value object=$_reglement field=montant}}
      </td>
      {{if $_reglement->lock}}
        <td>{{mb_value object=$_reglement field=date}}</td>
        <td> <button class="lock notext" disabled>{{mb_label object=$_reglement field=lock}}</button></td>
      {{else}}
        <td>
          <input type="hidden" name="date_{{$_reglement->_id}}" class="{{$_reglement->_props.date}}" value="{{$_reglement->date}}" />
          <button type="button" class="submit notext" onclick="editReglementDate('{{$_reglement->_id}}', this.up('td').down('input[name=date_{{$_reglement->_id}}]').value);"></button>
          <script>
            Main.add(function(){
              Calendar.regField(getForm("reglement-add").date_{{$_reglement->_id}});
            });
          </script>
        </td>
        <td>
          <button type="button" class="remove notext" onclick="delReglement('{{$_reglement->_id}}', '{{$_reglement->object_class}}', '{{$_reglement->object_id}}');""></button>
        </td>
      {{/if}}
    </tr>
    {{/foreach}}
    {{if ($object->_du_restant_patient) > 0 || $conf.dPfacturation.CReglement.use_lock_acquittement}}
      <tr>
        <td>
          <select name="mode" onchange="updateBanque(this.value);" >
            <option value="">&mdash; Choisir</option>
            {{foreach from=$reglement->_specs.mode->_locales item=num key=key}}
              <option value="{{$key}}" {{if $conf.dPfacturation.CReglement.use_mode_default == $key}}selected{{/if}}>{{$num}}</option>
            {{/foreach}}
          </select>
          {{mb_field object=$reglement field=banque_id options=$banques style="display: none"}}
          {{if isset($object->_num_bvr|smarty:nodefaults)}}
            <select name="num_bvr" style="display:none;" onchange="modifMontantBVR(this.value);" >
              <option value="0">&mdash; Choisir un num�ro</option>
              {{foreach from=$object->_num_bvr item=num}}
                <option value="{{$num}}">{{$num}}</option>
              {{/foreach}}
            </select>
          {{/if}}
        </td>
        <td>{{mb_field object=$reglement field=reference style="display: none"}}</td>
        {{if $conf.dPfacturation.CReglement.use_debiteur}}
          <td>
            <select name="debiteur_id" onchange="updateDebiteur(this.value);" style="max-width: 150px;">
              <option value="">&mdash; Choisir un d�biteur</option>
              {{foreach from=$object->_ref_debiteurs item=debiteur}}
                <option value="{{$debiteur->_id}}">{{$debiteur}}</option>
              {{/foreach}}
            </select>
          </td>
          <td id="reload_debiteur_desc">{{mb_field object=$reglement field=debiteur_desc}}</td>
        {{else}}
          <td>{{mb_field object=$reglement field=tireur}}</td>
        {{/if}}
        <td><input type="text" class="currency notNull" size="4" maxlength="8" name="montant" value="{{$object->_du_restant_patient}}" /></td>
        <td>{{mb_field object=$reglement field=date register=true form="reglement-add" value="now"}}</td>
        <td>
          <button id="reglement_button_add" class="add notext" type="submit">{{tr}}Add{{/tr}}</button>
        </td>
      </tr>
    {{/if}}
    <tr>
      <td colspan="7" style="text-align: center;">
        {{mb_value object=$object field=_reglements_total_patient}} r�gl�s, 
        <strong id="reglements_strong_value">{{mb_value object=$object field=_du_restant_patient}} restant</strong>
      </td>
    </tr>
    <tr>
      <td colspan="7" style="text-align: center;">
        <strong>
          {{mb_label object=$object field=patient_date_reglement}}
          <input type="hidden" name="patient_date_reglement" class="date" value="{{$object->patient_date_reglement}}" />
          <button type="button" class="submit notext" onclick="editAquittementDate(this.up('td').down('input[name=patient_date_reglement]').value);"></button>
        </strong>
        <script>
          Main.add(function(){
            Calendar.regField(getForm("reglement-add").patient_date_reglement);
          });
        </script>
      </td>
    </tr>
  </table>
</form>