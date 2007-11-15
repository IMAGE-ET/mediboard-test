<strong>Addictions significatifs</strong>

<ul>
{{if $sejour->_ref_dossier_medical->_ref_addictions}}
  {{foreach from=$sejour->_ref_dossier_medical->_ref_types_addiction key=curr_type item=list_addiction}}
  {{if $list_addiction|@count}}
  <li>
    {{tr}}CAddiction.type.{{$curr_type}}{{/tr}}
    {{foreach from=$list_addiction item=curr_addiction}}
    <ul>
      <li>
        <form name="delAddictionFrm-{{$curr_addiction->_id}}" action="?m=dPcabinet" method="post">

        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_addiction_aed" />
        {{mb_field object=$curr_addiction field="addiction_id" hidden=1 prop=""}}
        <button class="trash notext" type="button" onclick="Addiction.remove(this.form, reloadDossierMedicalSejour)">
        {{tr}}Delete{{/tr}}
        </button>
        {{$curr_addiction->addiction}}
       </form>
      </li>
    </ul>
    {{/foreach}}
  </li>
  {{/if}}
  {{/foreach}}
{{else}}
  <li><em>Pas d'addictions</em></li>
{{/if}}
</ul>