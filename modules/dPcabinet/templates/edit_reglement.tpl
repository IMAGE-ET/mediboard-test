{{assign var=form value="Edit-`$reglement->_guid`"}}

<form name="{{$form}}" method="post" onsubmit="return onSubmitFormAjax(this, Control.Modal.close);">
  {{mb_class object=$reglement}}
  {{mb_key   object=$reglement}}
  {{mb_field object=$reglement field=object_class     hidden=1}}
  {{mb_field object=$reglement field=object_id        hidden=1}}
  {{mb_field object=$reglement field=_force_regle_acte value=$force_regle_acte hidden=1}}
  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$reglement}}
  
    <tr>
      <th>{{mb_label object=$reglement field=object_id}}</th>
      <td><strong>{{$facture}}</strong></td>
    </tr>
    <tr>
      <th>{{mb_label object=$reglement field=date}}</th>
      <td>{{mb_field object=$reglement field=date form=$form register=true}}</td>
    </tr>
  
    <tr>
      <th>{{mb_label object=$reglement field=emetteur}}</th>
      <td>{{mb_field object=$reglement field=emetteur typeEnum=radio}}</td>
    </tr>
  
    <tr>
      <th>{{mb_label object=$reglement field=mode}}</th>
      <td>
        {{mb_field object=$reglement field=mode typeEnum=radio onchange="Reglement.updateBanque(this)"}}
        <script>Reglement.regBanque('{{$form}}', '{{$reglement->mode}}');</script>
      </td>
    </tr>
  
    <tr id="choice_banque">
      <th>{{mb_label object=$reglement field=banque_id}}</th>
      <td>{{mb_field object=$reglement field=banque_id options=$banques}}</td>
    </tr>
    
    <tr id="numero_bvr">
      <th>{{mb_label object=$reglement field=num_bvr}}</th>
      <td>
        <select name="num_bvr">
          <option value="0">&mdash; Choisir un numéro</option>
          {{foreach from=$facture->_num_bvr item=num}}
            <option value="{{$num}}" {{if $reglement->num_bvr == $num}}selected="selected"{{/if}}>{{$num}}</option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$reglement field=montant}}</th>
      <td>{{mb_field object=$reglement field=montant}}</td>
    </tr>
    
    <tr>
      <td class="button" colspan="2">
        {{if $reglement->_id}}
        <button class="modify" type="submit">
          {{tr}}Save{{/tr}}
        </button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form, { objName:'{{$reglement->_view|smarty:nodefaults|JSAttribute}}'}, Control.Modal.close);">
          {{tr}}Delete{{/tr}}
        </button>
        {{else}}
        <button class="submit" type="submit">
          {{tr}}Create{{/tr}}
        </button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>