<form name="editSortiePrevue" method="post" action="?m={{$m}}">
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
  {{if $rpu->sortie_autorisee}}
    {{tr}}CRPU-sortie_assuree.1{{/tr}} à {{mb_field object=$sejour field="sortie_prevue" register=true form="editSortiePrevue" onchange="submitFormAjax(this.form, 'systemMsg')"}}
  {{else}}
    <input type="hidden" name="sortie_prevue" value="{{$now}}" />
    <button class="tick" type="button" onclick="submitSejRpuConsult();">{{mb_label object=$rpu field="sortie_autorisee"}}</button>
  {{/if}}
</form>