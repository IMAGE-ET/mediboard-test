<form name="editSortiePrevue" method="post" action="?m={{$m}}">
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="_modifier_sortie" value="0" />
  {{if $rpu->sortie_autorisee}}
    {{tr}}CRPU-sortie_assuree.1{{/tr}} à {{mb_field object=$sejour field="sortie_prevue" register=true form="editSortiePrevue" onchange="submitFormAjax(this.form, 'systemMsg')"}}
  {{else}}
    <input type="hidden" name="sortie_prevue" value="{{$now}}" />
    <button class="tick" type="button" onclick="submitSejRpuConsult();">{{mb_label object=$rpu field="sortie_autorisee"}}</button>
    <button class="tick" type="button" onclick="this.form.elements._modifier_sortie.value=1; validCotation(); submitSejRpuConsult();">Autoriser et effectuer la sortie</button>
  {{/if}}
</form>

<form name="formValidCotation" action="" method="post"> 
  <input type="hidden" name="dosql" value="do_consultation_aed" />
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="consultation_id" value="{{$consult->_id}}" />
  <input type="hidden" name="valide" value="1" />
</form>