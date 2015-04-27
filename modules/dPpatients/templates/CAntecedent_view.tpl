{{if !$object->_can->read}}
  <div class="small-info">
    {{tr}}{{$object->_class}}{{/tr}} : {{tr}}access-forbidden{{/tr}}
  </div>
  {{mb_return}}
{{/if}}

{{mb_script module=patients script=antecedents}}

<table class="tbl">
  <tr>
    <th class="category">{{tr}}CAntecedent{{/tr}}
      {{mb_include module=system template=inc_object_idsante400 object=$object}}
    </th>
  </tr>
  
  {{if $object->type}}
  <tr>
    <td>
      <strong>{{mb_label object=$object field=type}}</strong>
      {{mb_value object=$object field=type}}
      <br/>
    </td>
  </tr>
  {{/if}}

  {{if $object->appareil}}
  <tr>
    <td>
      <strong>{{mb_label object=$object field=appareil}}</strong>
      {{mb_value object=$object field=appareil}}
      <br/>
    </td>
  </tr>
  {{/if}}

  {{if $object->date}}
  <tr>
    <td>
      <strong>{{mb_label object=$object field=date}}</strong>
      {{mb_value object=$object field=date}}
      <br/>
    </td>
  </tr>
  {{/if}}

  {{if $object->rques}}
  <tr>
    <td class="text">
      <strong>{{mb_label object=$object field=rques}}</strong>
      {{mb_value object=$object field=rques}}
      <br/>
    </td>
  </tr>
  {{/if}}
</table>

{{assign var=dossier_medical value=$object->_ref_dossier_medical}}
<table class="form">
  <tr>
    <td class="button">
      {{if $dossier_medical->object_class == "CPatient"}}
        {{assign var=reload value="DossierMedical.reloadDossierPatient"}}
      {{else}}
        {{assign var=reload value="DossierMedical.reloadDossierSejour"}}
      {{/if}}
      
      <form name="Del-{{$object->_guid}}" action="?m=dPcabinet" method="post">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_antecedent_aed" />
        
        {{mb_key object=$object}}
        
        <input type="hidden" name="antecedent_id" value="{{$object->_id}}" />
        <input type="hidden" name="annule" value="" />
        <input type="hidden" name="reload" value="{{$reload}}" />

        {{if $object->annule == 0}}
          <button title="{{tr}}Cancel{{/tr}}" class="cancel" type="button" onclick="Antecedent.cancel(this.form, {{$reload}}); Antecedent.closeTooltip('{{$object->_guid}}');">
            {{tr}}Cancel{{/tr}}
          </button>
        {{else}}
          <button title="{{tr}}Restore{{/tr}}" class="tick" type="button" onclick="Antecedent.restore(this.form, {{$reload}}); Antecedent.closeTooltip('{{$object->_guid}}');">
            {{tr}}Restore{{/tr}}
          </button>
        {{/if}}
        
        {{if $object->owner_id == $app->user_id}}
          {{if $dossier_medical->object_class == "CPatient"}}
          <button type="button" class="edit"
            onclick="Antecedent.editAntecedents('{{$dossier_medical->object_id}}', '', '{{$reload}}', '{{$object->_id}}')">
            {{tr}}Edit{{/tr}}
          </button>
          {{/if}}
          <button title="{{tr}}Delete{{/tr}}" class="trash" type="button" onclick="Antecedent.remove(this.form, {{$reload}}); Antecedent.closeTooltip('{{$object->_guid}}');">
            {{tr}}Delete{{/tr}}
          </button>
        {{elseif $object->annule == 0 && $dossier_medical->object_class == "CPatient"}}
          <button title="{{tr}}Delete{{/tr}}" class="duplicate" type="button" onclick="Antecedent.duplicate(this.form); Antecedent.closeTooltip('{{$object->_guid}}');">
            {{tr}}Cancel{{/tr}} {{tr}}and{{/tr}} {{tr}}Modify{{/tr}}
          </button>
        {{/if}}
      </form>
    </td>
  </tr>

</table>
  
<table class="tbl" id="{{$object->_guid}}_tooltip">

  <tr>
    <th colspan="2">Historique</th>
  </tr>
  
  {{foreach from=$object->_ref_logs item=_log}}
  <tr>
    <td>{{$_log->_ref_user->_view}}
    <td>{{mb_value object=$_log field=date}}</td>
  </tr>
  {{/foreach}}
  
</table>