{{if !$object->_can->read}}
  <div class="small-info">
    {{tr}}{{$object->_class}}{{/tr}} : {{tr}}access-forbidden{{/tr}}
  </div>
  {{mb_return}}
{{/if}}

{{mb_script module=patients script=antecedents}}

{{mb_include template=CMbObject_view}}

{{if $object->annule == 1}}
<table class="tbl">
  <tr>
    <th class="category cancelled" colspan="3">
      {{tr}}CAntecedent-annule{{/tr}}
    </th>
  </tr>
</table>
{{/if}}

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
