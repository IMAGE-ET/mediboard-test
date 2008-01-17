<form name="editDP-{{$sejour->sejour_id}}" action="?m={{$m}}" method="post">
<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="dosql" value="do_sejour_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="sejour_id" value="{{$sejour->sejour_id}}" />
<input type="hidden" name="_praticien_id" value="{{$sejour->praticien_id}}" />
Diagnostic principal :
<input type="text" name="DP" value="{{$sejour->DP}}" size="5" onchange="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { reloadDiagnostic({{$sejour->_id}}, 1) } })" />
<button class="modify" type="button" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { reloadDiagnostic({{$sejour->_id}}, 1) } })">
  Valider
</button>
<button class="search notext" type="button" onclick="CIM10Selector.initDP({{$sejour->_id}})">
  Chercher un diagnostic
</button>
</form>
<br />
{{if $sejour->_ext_diagnostic_principal}}
<strong>{{$sejour->_ext_diagnostic_principal->libelle}}</strong>
{{/if}}
<br />
Diagnostics signicatifs :
<form name="editDossierMedical-{{$sejour->_id}}" action="?m={{$m}}" method="post">
<input type="hidden" name="m" value="dPpatients" />
<input type="hidden" name="dosql" value="do_dossierMedical_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="object_class" value="CSejour" />
<input type="hidden" name="object_id" value="{{$sejour->_id}}" />
<input type="hidden" name="_praticien_id" value="{{$sejour->praticien_id}}" />
<input type="text" name="_added_code_cim" size="5" onchange="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { reloadDiagnostic({{$sejour->_id}}, 1) } })" />
<button class="modify" type="button" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { reloadDiagnostic({{$sejour->_id}}, 1) } })">
  Valider
</button>
<button class="search notext" type="button" onclick="CIM10Selector.initDAS({{$sejour->_id}})">
  Chercher un diagnostic
</button>
</form>
<br />
{{foreach from=$sejour->_ref_dossier_medical->_ext_codes_cim item="curr_cim"}}
  <form name="delCodeAsso-{{$curr_cim->code}}" action="?m={{$m}}" method="post">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="object_class" value="CSejour" />
  <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="_deleted_code_cim" value="{{$curr_cim->code}}" />
  <button class="trash notext" type="button" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { reloadDiagnostic({{$sejour->_id}}, 1) } })">
    {{tr}}Delete{{/tr}}
  </button>
  </form>
  {{$curr_cim->code}} : {{$curr_cim->libelle}}
  <br />
{{/foreach}}