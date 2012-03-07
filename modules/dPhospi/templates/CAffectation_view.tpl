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
    <tr>
      <th colspan="3">
        {{mb_include module=system template=inc_object_notes     }}
        {{mb_include module=system template=inc_object_idsante400}}
        {{mb_include module=system template=inc_object_history   }}
        {{$object}} {{mb_include module=system template=inc_interval_datetime from=$object->entree to=$object->sortie}}
      </th>
    </tr>
      <td rowspan="3">
        {{mb_include module=patients template=inc_vw_photo_identite mode=read patient=$patient size=50}}
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
                {{if $_affectation->_id == $object->_id}}
                  <strong>
                {{else}}
                  <span onmouseover="ObjectTooltip.createEx(this, '{{$_affectation->_guid}}')">
                {{/if}}
                {{$_affectation}} {{mb_include module=system template=inc_interval_datetime from=$_affectation->entree to=$_affectation->sortie}}
                {{if $_affectation->_id == $object->_id}}
                  </strong>
                {{else}}
                  </span>
                {{/if}}
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
                  Intervention du {{mb_value object=$_operation field=_datetime}}
                </span>
              </li>
            {{/foreach}}
          </ul>
        {{/if}}
      </td>
    </tr>
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
          onclick="if (window.delAffectation) { delAffectation('{{$object->_id}}') }">{{tr}}Delete{{/tr}}</button>
        {{if $object->sejour_id}}
          <button type="button" class="search" onclick="editPrestations('{{$object->sejour_id}}')">Prestations</button>
        {{/if}}
      </td>
    </tr>
  </table>
{{/if}}