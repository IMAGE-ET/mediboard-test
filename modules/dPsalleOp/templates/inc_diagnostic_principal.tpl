{{mb_include_script module="dPplanningOp" script="cim10_selector"}}

<script type="text/javascript">

onSubmitDiag = function(oForm) {
	return onSubmitFormAjax(oForm, { 
		onComplete: function() { 
			reloadDiagnostic({{$sejour->_id}}, {{$modeDAS}}) 
		} 
	} );
}
</script>

<table class="form">
  <tr>
    <th class="category" style="width: 50%">{{mb_label object=$sejour field="DP"}}</th>
    <th class="category" style="width: 50%">{{mb_label object=$sejour field="DR"}}</th>
  </tr>
  <tr>
		<!-- Diagnostic Principal -->
    <td class="button">

      <form name="editSejourDP" action="?m={{$m}}" method="post" onsubmit="return onSubmitDiag(this);">

      <input type="hidden" name="m" value="dPplanningOp" />
      <input type="hidden" name="dosql" value="do_sejour_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
      <input type="hidden" name="praticien_id" value="{{$sejour->praticien_id}}" />
      <button type="button" class="search notext" onclick="CIM10Selector.initDP()">
        {{tr}}button-CCodeCIM10-choix{{/tr}}
      </button>
      <script type="text/javascript">
        CIM10Selector.initDP = function() {
          this.sForm     = "editSejourDP";
          this.sView     = "DP";
          this.sChir     = "praticien_id";
          this.pop();
        }
      </script>
      <input type="text" name="DP" value="{{$sejour->DP}}" class="code cim10" size="10" onchange="this.form.onsubmit()" />
      <button class="tick notext" type="button">{{tr}}Validate{{/tr}}</button>
      </form>
    </td>
    
		<!-- Diagnostic Relié -->
    <td class="button">

      <form name="editSejourDR" action="?m={{$m}}" method="post" onsubmit="return onSubmitDiag(this);">

      <input type="hidden" name="m" value="dPplanningOp" />
      <input type="hidden" name="dosql" value="do_sejour_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
      <input type="hidden" name="praticien_id" value="{{$sejour->praticien_id}}" />
      <button type="button" class="search notext" onclick="CIM10Selector.initDR()">
        {{tr}}button-CCodeCIM10-choix{{/tr}}
      </button>
      <script type="text/javascript">
        CIM10Selector.initDR = function() {
          this.sForm     = "editSejourDR";
          this.sView     = "DR";
          this.sChir     = "praticien_id";
          this.pop();
        }
      </script>
      <input type="text" name="DR" value="{{$sejour->DR}}" class="code cim10" size="10" onchange="this.form.onsubmit()" />
      <button class="tick notext" type="button">{{tr}}Validate{{/tr}}</button>
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
      <form name="editDossierMedical" action="?m={{$m}}" method="post" onsubmit="return onSubmitDiag(this);">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="object_class" value="CSejour" />
        <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
        <input type="hidden" name="_praticien_id" value="{{$sejour->praticien_id}}" />
        <button class="search notext" type="button" onclick="CIM10Selector.initAsso()">
          Chercher un diagnostic
        </button>
        <input type="text" name="_added_code_cim" size="5" onchange="this.form.onsubmit()" />
        <button class="tick notext" type="button">
          Valider
        </button>
        <script type="text/javascript">   
          CIM10Selector.initAsso = function(){
            this.sForm = "editDossierMedical";
            this.sView = "_added_code_cim";
            this.sChir = "_praticien_id";
            this.pop();
          }
        </script> 
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
