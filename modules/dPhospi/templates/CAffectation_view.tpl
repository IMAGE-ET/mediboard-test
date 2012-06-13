{{mb_script module=planningOp script=prestations ajax=1}}

{{assign var=sejour     value=$object->_ref_sejour}}
{{assign var=patient    value=$sejour->_ref_patient}}
{{assign var=operations value=$sejour->_ref_operations}}
{{assign var=affectations value=$sejour->_ref_affectations}}

<table class="tbl">
  {{if $object->sejour_id}}
    {{mb_include module=dPplanningOp template=inc_sejour_affectation_view}}
  {{else}}
    <tr>
      <th>
        Lit bloqué {{mb_include module=system template=inc_interval_datetime from=$object->entree to=$object->sortie}}
      </th>
    </tr>
  {{/if}}
</table>

{{if $can->edit}}
  <table class="tbl">
    <tr>
      <td class="button">
        <button type="button" class="edit"
          onclick="if (window.editAffectation) { this.up('div').hide(); editAffectation('{{$object->_id}}') }">Modifier</button>
        <button type="button" class="cancel"
          onclick="if (window.delAffectation) { delAffectation('{{$object->_id}}', '{{$object->lit_id}}', 'CSejour-{{$object->sejour_id}}') }">{{tr}}Delete{{/tr}}</button>
        {{if $object->sejour_id && $conf.dPhospi.systeme_prestations == "expert"}}
          <button type="button" class="search" onclick="Prestations.edit('{{$object->sejour_id}}')">Prestations</button>
        {{/if}}
      </td>
    </tr>
  </table>
{{/if}}