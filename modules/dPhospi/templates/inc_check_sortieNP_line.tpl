<tr>
  <td class="text">
   {{assign var=sejour value=$_sortie}}
   {{if $canPlanningOp->read}}
   <a class="action" style="float: right"  title="Modifier le séjour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$sejour->_id}}">
     <img src="images/icons/planning.png" alt="modifier" />
   </a>
   {{/if}}
    {{assign var=patient value=$sejour->_ref_patient}}
    <strong onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')"
      {{if !$sejour->entree_reelle}} class="patient-not-arrived"{{/if}}>
      {{$patient}}
    </strong>
  </td>
  <td class="text">
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$sejour->_ref_praticien}}
  </td>
  {{if $type == "presents"}}
  <td>{{$sejour->sortie|date_format:$conf.datetime}}</td>
  {{/if}}
  <td>
    <div {{if !$sejour->sortie_reelle}}class="only-printable"{{/if}}>
      {{if $type == 'presents'}}
        {{$sejour->sortie|date_format:$conf.datetime}}
      {{else}}
        {{$sejour->sortie|date_format:$conf.time}}
      {{/if}}
    </div>
    {{if !$sejour->sortie_reelle}}
      <div class="not-printable">
      {{assign var=sejour_guid value=$sejour->_guid}}
      <form name="editSortiePrevue-{{$type}}-{{$sejour_guid}}" method="post" action="?"
        onsubmit="return onSubmitFormAjax(this, { onComplete: function() { refreshList(null, null, '{{$type}}');} })">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_sejour_aed" />
        <input type="hidden" name="del" value="0" />
        {{mb_key object=$sejour}}
        {{mb_field object=$sejour field=entree_prevue hidden=true}}
        <button class="add" type="button" onclick="addDays(this, 1)">1J</button>
        {{mb_field object=$sejour field=sortie_prevue register=true form="editSortiePrevue-$type-$sejour_guid" onchange="this.form.onsubmit()"}}
      </form>
      </div>
    {{/if}}
  </td>
</tr>