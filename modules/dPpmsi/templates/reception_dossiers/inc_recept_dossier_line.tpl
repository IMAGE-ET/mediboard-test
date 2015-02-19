{{mb_script module="pmsi" script="reception" ajax=true}}

<script>
  Main.add(function() {
    Reception.form = 'selType';
  });
</script>

{{assign var=patient value=$_sejour->_ref_patient}}
{{assign var=sejour_id value=$_sejour->_id}}
<td class="text">
  {{mb_value object=$_sejour field="sortie_reelle"}}
</td>

<td colspan="2">
  {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$_sejour _show_numdoss_modal=1}}
  <span class="CPatient-view" onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">
    {{$patient}}
  </span>
</td>

<td class="text">
  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
</td>

<td>
  <span class="CSejour-view" onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
    {{$_sejour->_shortview}}
  </span>
</td>

<td class="button">
  <form name="sejour_reception_sortie_{{$sejour_id}}" action="?" method="post">
    {{mb_class object=$_sejour}}
    {{mb_key   object=$_sejour}}

    {{if $_sejour->reception_sortie}}
      {{mb_field object=$_sejour field="reception_sortie" form="sejour_reception_sortie_$sejour_id" register=true onchange="Reception.subitEtatPmsi(this.form, '$sejour_id');"}}
    {{else}}
      <input type="hidden" name="reception_sortie" value="now"/>
      <button class="tick" type="button" onclick="Reception.subitEtatPmsi(this.form, '{{$sejour_id}}');">Réception du dossier</button>
    {{/if}}
  </form>
</td>

<td class="button">
  <form name="sejour_completion_sortie_{{$sejour_id}}" action="?" method="post">
    {{mb_class object=$_sejour}}
    {{mb_key   object=$_sejour}}

    {{if $_sejour->completion_sortie}}
      {{mb_field object=$_sejour field="completion_sortie" form="sejour_completion_sortie_$sejour_id" register=true onchange="Reception.subitEtatPmsi(this.form, '$sejour_id');"}}
    {{else}}
      <input type="hidden" name="completion_sortie" value="now"/>
      <button class="tick" type="button" onclick="Reception.subitEtatPmsi(this.form, '{{$sejour_id}}');">Dossier complété</button>
    {{/if}}
  </form>
</td>