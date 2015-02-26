{{mb_script module=dPhospi script=infrastructure ajax=true}}
<form name="edit{{$chambre->_guid}}" action="" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: Control.Modal.close.curry()});">
  {{mb_key object=$chambre}}
  {{mb_class object=$chambre}}
  {{if !$chambre->_id}}
    <input type="hidden" name="callback"  value="Infrastructure.addeditChambreCallback"/>
  {{/if}}
  <table class="form">
    {{mb_include module=system template=inc_form_table_header_uf object=$chambre tag=$tag_chambre}}

    <tr>
      <th>{{mb_label object=$chambre field=nom}}</th>
      <td>{{mb_field object=$chambre field=nom}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$chambre field=service_id}}</th>
      <td>
        {{if $chambre->_id}}
          {{$chambre->_ref_service->_view}}
        {{else}}
          {{mb_field object=$chambre field=service_id options=$services}}
        {{/if}}
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$chambre field=caracteristiques}}</th>
      <td>{{mb_field object=$chambre field=caracteristiques}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$chambre field=rank}}</th>
      <td>{{mb_field object=$chambre field=rank increment=true form="edit`$chambre->_guid`"}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$chambre field=lits_alpha}}</th>
      <td>{{mb_field object=$chambre field=lits_alpha}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$chambre field=annule}}</th>
      <td>{{mb_field object=$chambre field=annule}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$chambre field=is_waiting_room}}</th>
      <td>{{mb_field object=$chambre field=is_waiting_room}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$chambre field=is_examination_room}}</th>
      <td>{{mb_field object=$chambre field=is_examination_room}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$chambre field=is_sas_dechoc}}</th>
      <td>{{mb_field object=$chambre field=is_sas_dechoc}}</td>
    </tr>

    <tr>
      <td class="button" colspan="2">
        {{if $chambre->_id}}
          <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>
          <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la chambre',objName: $V(this.form.nom) })">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
          <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>

{{if $chambre->_id}}
  <!-- Liste des lits de la chambre -->
 {{mb_include module=dPhospi template=inc_vw_lits}}
{{/if}}