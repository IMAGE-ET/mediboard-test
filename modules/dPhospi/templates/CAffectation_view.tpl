<script type="text/javascript">
  editPrestations = function (sejour_id) {
    var url = new Url("dPplanningOp", "ajax_vw_prestations");
    url.addParam("sejour_id", sejour_id);
    url.requestModal(800, 700);
  }
</script>

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
          onclick="if (window.editAffectation) { editAffectation('{{$object->_id}}') }">Modifier</button>
        <button type="button" class="cancel"
          onclick="if (window.delAffectation) { delAffectation('{{$object->_id}}', '{{$object->lit_id}}') }">{{tr}}Delete{{/tr}}</button>
        {{if $object->sejour_id}}
          <button type="button" class="search" onclick="editPrestations('{{$object->sejour_id}}')">Prestations</button>
        {{/if}}
      </td>
    </tr>
  </table>
{{/if}}