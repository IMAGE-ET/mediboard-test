<!--  Diagnostic Principal -->
<form name="editDP-{{$sejour->_id}}" action="?m={{$m}}" method="post" 
      onsubmit="return onSubmitFormAjax(this, { onComplete: reloadDiagnostic.curry({{$sejour->_id}}, 1) })">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="_praticien_id" value="{{$sejour->praticien_id}}" />
  
  <div style="text-align: right;">
    {{mb_label object=$sejour field=DP}}
    {{mb_field object=$sejour field=DP size=5}}
    <button class="modify notext" type="submit">
      {{tr}}Validate{{/tr}}
    </button>
    <button class="search notext" type="button" onclick="CIM10Selector.initDP({{$sejour->_id}})">
      {{tr}}Search{{/tr}}
    </button>
  </div>
</form>

{{if $sejour->_ext_diagnostic_principal}}
  <strong>{{$sejour->_ext_diagnostic_principal->libelle}}</strong>
{{/if}}
<hr />

<!--  Diagnostic Relié -->
<form name="editDR-{{$sejour->_id}}" action="?m={{$m}}" method="post"
      onsubmit="return onSubmitFormAjax(this, { onComplete: reloadDiagnostic.curry({{$sejour->_id}}, 1) })">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="_praticien_id" value="{{$sejour->praticien_id}}" />
  
  <div style="text-align: right;">
    {{mb_label object=$sejour field=DR}}
    {{mb_field object=$sejour field=DR size=5}}
    <button class="modify notext" type="submit">
      {{tr}}Validate{{/tr}}
    </button>
    <button class="search notext" type="button" onclick="CIM10Selector.initDR({{$sejour->_id}})">
      {{tr}}Search{{/tr}}
    </button>
  </div>
</form>

{{if $sejour->_ext_diagnostic_relie}}
  <strong>{{$sejour->_ext_diagnostic_relie->libelle}}</strong>
{{/if}}
<hr />

<form name="editDossierMedical-{{$sejour->_id}}" action="?m={{$m}}" method="post"
      onsubmit="return onSubmitFormAjax(this, { onComplete: reloadDiagnostic.curry({{$sejour->_id}}, 1) })">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="object_class" value="CSejour" />
  <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="_praticien_id" value="{{$sejour->praticien_id}}" />
  
  <div style="text-align: right;">
    <label for="_added_code_cim" title="Diagnostics associés significatifs">DAS</label>
    <input type="text" name="_added_code_cim" size="5" />
    <button class="add notext" type="submit">
      {{tr}}Validate{{/tr}}
    </button>
    <button class="search notext" type="button" onclick="CIM10Selector.initDAS({{$sejour->_id}})">
      {{tr}}Search{{/tr}}
    </button>
  </div>
</form>

<br />
{{foreach from=$sejour->_ref_dossier_medical->_ext_codes_cim item="curr_cim"}}
<form name="delCodeAsso-{{$curr_cim->code}}" action="?m={{$m}}" method="post"
      onsubmit="return onSubmitFormAjax(this, { onComplete: reloadDiagnostic.curry({{$sejour->_id}}, 1) })">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="object_class" value="CSejour" />
  <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="_deleted_code_cim" value="{{$curr_cim->code}}" />
  <button class="trash notext" type="submit">
    {{tr}}Delete{{/tr}}
  </button>
</form>
  {{$curr_cim->code}} : {{$curr_cim->libelle}}
  <br />
{{/foreach}}