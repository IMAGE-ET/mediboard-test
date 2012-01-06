<script type="text/javascript">
  editPrestations = function (sejour_id) {
    var url = new Url("dPplanningOp", "ajax_vw_prestations");
    url.addParam("sejour_id", sejour_id);
    url.requestModal(800);
  }
</script>
{{*
{{mb_include module=system template=CMbObject_view}}
 *}}
{{assign var=sejour     value=$object->_ref_sejour}}
{{assign var=patient    value=$sejour->_ref_patient}}
{{assign var=operations value=$sejour->_ref_operations}}
{{assign var=affectations value=$sejour->_ref_affectations}}

<table class="tbl">
  <tr>
    <th colspan="3">
      {{$object}} du {{$object->entree|date_format:$conf.date}} au {{$object->sortie|date_format:$conf.date}}
    </th>
  </tr>
    <td rowspan="3">
      {{mb_include module=dPpatients template=inc_vw_photo_identite mode=read patient=$patient size=50}}
    </td>
    <td>
      <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
        {{$patient->nom}} {{$patient->prenom}}
      </span>
    </td>
    <td>
      {{mb_value object=$sejour field=entree}}
    </td>
  </tr>
  <tr>
    <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$sejour->_ref_praticien}}</td>
    <td>{{mb_value object=$sejour field=sortie}}</td>
  </tr>
  <tr>
    <td colspan="2" class="text">
      {{mb_label object=$sejour field=libelle}} : {{mb_value object=$sejour field=_motif_complet}}
    </td>
  </tr>
  {{if $sejour->_couvert_cmu || $sejour->_couvert_ald}}
    <tr>
      <td colspan="3">
        {{if $sejour->_couvert_cmu}}CMU /{{/if}} {{if $sejour->_couvert_ald}}ALD{{/if}} 
      </td>
    </tr>
  {{/if}}
  {{*
  <tr>
    <!-- Prestations -->
    <td colspan="2"></td>
  </tr>
   *}}
  <tr>
    <td colspan="3">
      {{if $affectations|@count}}
        Affectations :
        <ul>
          {{foreach from=$affectations item=_affectation}}
            <li>
              <span onmouseover="ObjectTooltip.createEx(this, '{{$_affectation->_guid}}')">
                Affectation du {{$_affectation->entree|date_format:$conf.date}} au {{$_affectation->sortie|date_format:$conf.date}}
              </span>
            </li>
          {{/foreach}}
        </ul>
      {{/if}}
      {{if $operations|@count}}
        Interventions :
        <ul>
          {{foreach from=$operations item=_operation}}
            <li>
              <span onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_guid}}')">
                Intervention du {{$_operation->_datetime|date_format:$conf.date}}
              </span>
            </li>
          {{/foreach}}
        </ul>
      {{/if}}
    </td>
  </tr>
</table>

{{if $can->edit}}
  <table class="tbl">
    <tr>
      <td class="button">
        <button type="button" class="edit"
          onclick="if (window.editAffectation) { editAffectation('{{$object->_id}}') }">Modifier</button>
        <button type="button" class="cancel"
          onclick="if (window.delAffectation) { delAffectation('{{$object->_id}}') }">{{tr}}Delete{{/tr}}</button>
        <button type="button" class="search" onclick="editPrestations('{{$object->sejour_id}}')">Prestations</button>
      </td>
    </tr>
  </table>
{{/if}}