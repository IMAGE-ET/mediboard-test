<script type="text/javascript">
var oFormClick = window.opener.document.forms.click;
var anyFormSubmitted = false;

function checkTransmission(form, trans){
  var formTrans = getForm(trans);
  if($V(form.quantite_prevue)-0 != $V(form.quantite)-0) {
    $(formTrans.text).addClassName('notNull');
  } else {
    $(formTrans.text).removeClassName('notNull');
  }
}

function submitAdministration(form, trans) {
  var formTrans = getForm(trans);
  if (!checkForm(formTrans) || !checkForm(form)) {
    return false;
  }
  submitFormAjax(form, 'systemMsg');
  anyFormSubmitted = true;
  return true;
}

function closeApplyAdministrations(dontClose) {
  {{if $administrations|@count && $sejour_id && $date_sel}}
    if (anyFormSubmitted && window.opener) {
      window.opener.loadTraitement('{{$sejour_id}}','{{$date_sel}}', oFormClick.nb_decalage.value);
    }
  {{/if}}
  if (!dontClose) {
    window.close();
  }
}

Main.add(function () {
  window.onbeforeunload = function () {
    closeApplyAdministrations (true);
  }
});
</script>

<button type="button" class="cancel" onclick="closeApplyAdministrations()">{{tr}}Close{{/tr}}</button>

<table class="form" id="administrations">
{{foreach from=$administrations item=adm key=line_id name=by_adm}}
  {{foreach from=$adm item=by_unite_prise key=unite_prise name=adm_by_unite_prise}}
    {{foreach from=$by_unite_prise item=by_date key=date}}
      {{foreach from=$by_date item=by_hour key=hour}}
      {{assign var=_unite value=$unite_prise|utf8_decode}}
      {{assign var=key value="$line_id-$_unite-$date-$hour"}}
      {{if $smarty.foreach.adm_by_unite_prise.first}}
      <tr>
        <th class="title" colspan="2">{{$by_hour.line->_view}}</th>
      </tr>
      {{/if}}
      <tr>
        <td style="text-align: right;" id="adm_{{$key}}">
          <form name="addAdministration_{{$key}}" method="post" action="?" onsubmit="return checkForm(this)" style="float: left;">
            <input type="hidden" name="dosql" value="do_administration_aed" />
            <input type="hidden" name="m" value="dPprescription" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="administration_id" value="" />
            <input type="hidden" name="administrateur_id" value="{{$app->user_id}}" />
            <input type="hidden" name="object_id" value="{{$by_hour.line->_id}}" />
            <input type="hidden" name="object_class" value="{{$by_hour.line->_class_name}}" />
            <input type="hidden" name="unite_prise" value="{{$by_hour.unite_prise}}" />
            <input type="hidden" name="dateTime" value="{{$by_hour.dateTime}}" />
            <input type="hidden" name="prise_id" value="{{$by_hour.prise_id}}" />
            <input type="hidden" name="quantite_prevue" disabled="disabled" value="{{$by_hour.prise->quantite}}" />
            <input type="hidden" name="callback" value="submitTransmission_{{$key}}" />
            <button type="button" class="add" onclick="return submitAdministration(this.form, 'editTrans_{{$key}}');">{{tr}}Add{{/tr}}</button>
            
            <b>{{$date|date_format:"%d/%m/%Y"}}, {{$hour}}h</b> : 
            {{mb_label object=$by_hour.prise field=quantite}}
            {{mb_field object=$by_hour.prise field=quantite min=1 increment=1 form="addAdministration_$key" onchange="checkTransmission(this.form, 'editTrans_$key')" onkeyup="checkTransmission(this.form, 'editTrans_$key')"}}
            
            {{if $by_hour.line->_class_name == "CPrescriptionLineMedicament"}}
              {{$by_hour.line->_ref_produit->libelle_unite_presentation}}
            {{else}}
              {{$by_hour.line->_unite_prise}}
            {{/if}}
          </form>
          
          <script type="text/javascript">
          // Fonction appelée en callback du formulaire d'administration
          window['submitTransmission_{{$key}}'] = function (administration_id) {
            oFormTransmission = getForm('editTrans_{{$key}}');
            oFormTransmission.object_class.value = "CAdministration";
            oFormTransmission.object_id.value = administration_id;
            if(oFormTransmission.text.value != '') {
              onSubmitFormAjax(oFormTransmission);
            }
            $('adm_{{$key}}').update('Administration effectuée');
          }
          </script>

          <form name="editTrans_{{$key}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)" style="width: 100%; clear: both;">
            <input type="hidden" name="dosql" value="do_transmission_aed" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="m" value="dPhospi" />
            <input type="hidden" name="object_class" value="" />
            <input type="hidden" name="object_id" value="" />
            <input type="hidden" name="sejour_id" value="{{$sejour_id}}" />
            <input type="hidden" name="user_id" value="{{$app->user_id}}" />
            <input type="hidden" name="date" value="now" />
            <div style="padding: 0.1em;">
              {{mb_label object=$transmission field="degre"}}
              {{mb_field object=$transmission field="degre"}}
            </div>
            {{mb_field object=$transmission field="text"}}
          </form>
        </td>
      </tr>
      {{/foreach}}
    {{/foreach}}
  {{/foreach}}
{{foreachelse}}
  <tr><td>Veuillez choisir au moins une prise</td></tr>
{{/foreach}}
</table>
