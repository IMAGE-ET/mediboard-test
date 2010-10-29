{{mb_include_script module="dPplanningOp" script="cim10_selector"}}

<script type="text/javascript">

onSubmitDiag = function(oForm) {
	return onSubmitFormAjax(oForm, { 
		onComplete: function() { 
			reloadDiagnostic({{$sejour->_id}}, {{$modeDAS}}) 
		} 
	} );
}

Main.add(function() {

    var url = new Url("dPcim10", "ajax_code_cim10_autocomplete");
    url.addParam("type", "editDP");
    url.autoComplete("editDP_keywords_code", '', {
      minChars: 1,
      dropdown: true,
      width: "250px",
    });

    var urlb = new Url("dPcim10", "ajax_code_cim10_autocomplete");
    urlb.addParam("type", "editDR");
    urlb.autoComplete("editDR_keywords_code", '', {
      minChars: 1,
      dropdown: true,
      width: "250px",
    });

    var urlc = new Url("dPcim10", "ajax_code_cim10_autocomplete");
    urlc.addParam("type", "editDA");
    urlc.autoComplete("editDA_keywords_code", '', {
      minChars: 1,
      dropdown: true,
      width: "250px",
    });
});
</script>

<table class="form">
  <tr>
    <th class="category" style="width: 50%">{{mb_label object=$sejour field="DP"}}</th>
    <th class="category" style="width: 50%">{{mb_label object=$sejour field="DR"}}</th>
  </tr>
  <tr>
		<!-- Diagnostic Principal -->
    <td class="button">

      <form name="editDP" action="?m={{$m}}" method="post" onsubmit="return onSubmitDiag(this);">
	      <input type="hidden" name="m" value="dPplanningOp" />
	      <input type="hidden" name="dosql" value="do_sejour_aed" />
	      <input type="hidden" name="del" value="0" />
	      <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
	      <input type="hidden" name="praticien_id" value="{{$sejour->praticien_id}}" />
	      <input type="hidden" name="DP" value='' onchange="this.form.onsubmit();"/>
	      <input type="text"   name="keywords_code" id="editDP_keywords_code" value="{{$sejour->DP}}" class="autocomplete str code cim10" size="10" />
      </form>
    </td>
    
		<!-- Diagnostic Relié -->
    <td class="button">

      <form name="editDR" action="?m={{$m}}" method="post" onsubmit="return onSubmitDiag(this);">
	      <input type="hidden" name="m" value="dPplanningOp" />
	      <input type="hidden" name="dosql" value="do_sejour_aed" />
	      <input type="hidden" name="del" value="0" />
	      <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
	      <input type="hidden" name="praticien_id" value="{{$sejour->praticien_id}}" />
	      <input type="hidden" name="DR" value='' onchange="this.form.onsubmit();"/>
	      <input type="text"   name="keywords_code" id="editDR_keywords_code"value="{{$sejour->DR}}" class="autocomplete str code cim10" size="10" />
      </form>
    </td>
  </tr>
  
  <tr>
    <td class="text button">
      {{if $sejour->_ext_diagnostic_principal}}
      <strong>{{$sejour->_ext_diagnostic_principal->libelle}}</strong>
      {{/if}}
    </td>
    <td class="text button">
      {{if $sejour->_ext_diagnostic_relie}}
      <strong>{{$sejour->_ext_diagnostic_relie->libelle}}</strong>
      {{/if}}
    </td>
  </tr>

  {{if $modeDAS}}
  <tr>
    <th class="category" colspan="2">
      Diagnostics associés ({{$sejour->_ref_dossier_medical->_ext_codes_cim|@count}})
    </th>
  </tr>
  <tr>
    <td class="button" colspan="2">
      <form name="editDA" action="?m={{$m}}" method="post" onsubmit="return onSubmitDiag(this);">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="object_class" value="CSejour" />
        <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
        <input type="hidden" name="_praticien_id" value="{{$sejour->praticien_id}}" />
        <input type="hidden" name="_added_code_cim" onchange="this.form.onsubmit();"/>
        <input type="text"   name="keywords_code" id="editDA_keywords_code" size="5" class="autocomplete str code cim10" />
      </form>
    </td>
  </tr>
  <tr>
    <td class="text" colspan="2">
      {{foreach from=$sejour->_ref_dossier_medical->_ext_codes_cim item="curr_cim"}}
      <form name="delCodeAsso-{{$curr_cim->code}}" action="?m={{$m}}" method="post" onsubmit="return onSubmitDiag(this);">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="object_class" value="CSejour" />
        <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
        <input type="hidden" name="_deleted_code_cim" value="{{$curr_cim->code}}" />
        <button class="trash notext" type="button" onclick="this.form.onsubmit()">
          {{tr}}Delete{{/tr}}
        </button>
      </form>
      {{$curr_cim->code}} : {{$curr_cim->libelle}}
      <br />
      {{/foreach}}
    </td>
  </tr>
  {{/if}}

</table>
