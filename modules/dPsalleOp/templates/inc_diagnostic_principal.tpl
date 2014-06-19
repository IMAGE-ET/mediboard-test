{{mb_script module="dPplanningOp" script="cim10_selector"}}

<script>
  onSubmitDiag = function(oForm) {
    return onSubmitFormAjax(oForm, reloadDiagnostic.curry({{$sejour->_id}}, {{$modeDAS}}));
  }

  deleteDiag = function(form, field) {
    $V(form.keywords_code, "");
    $V(form.elements[field], "");
    form.onsubmit();
  }

  Main.add(function() {
    var url = new Url("cim10", "ajax_code_cim10_autocomplete");
    url.autoComplete(getForm("editDP").keywords_code, '', {
      minChars: 1,
      dropdown: true,
      select: "code",
      width: "250px",
      afterUpdateElement: function(oHidden) {
        $V(getForm("editDP").DP, oHidden.value);
      }
    });

    var urlb = new Url("cim10", "ajax_code_cim10_autocomplete");
    urlb.autoComplete(getForm("editDR").keywords_code, '', {
      minChars: 1,
      dropdown: true,
      select: "code",
      width: "250px",
      afterUpdateElement: function(oHidden) {
        $V(getForm("editDR").DR, oHidden.value);
      }
    });

    {{if $modeDAS}}
      var urlc = new Url("dPcim10", "ajax_code_cim10_autocomplete");
      urlc.autoComplete(getForm("editDA").keywords_code, '', {
        minChars: 1,
        dropdown: true,
        select: "code",
        width: "250px",
        afterUpdateElement: function(oHidden) {
          $V(getForm("editDA")._added_code_cim, oHidden.value);
        }
      });
    {{/if}}
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
      <form name="editDP" action="?" method="post" onsubmit="return onSubmitDiag(this);">
	      <input type="hidden" name="m" value="planningOp" />
	      <input type="hidden" name="dosql" value="do_sejour_aed" />
	      <input type="hidden" name="del" value="0" />
        {{mb_key object=$sejour}}
	      <input type="hidden" name="praticien_id" value="{{$sejour->praticien_id}}" />
	      <input type="hidden" name="DP" value='' onchange="this.form.onsubmit();"/>
	      <input type="text"   name="keywords_code" value="{{$sejour->DP}}" class="autocomplete str code cim10" size="10" />
	      <button type="button" class="search notext" onclick="CIM10Selector.initDP()">
          {{tr}}button-CCodeCIM10-choix{{/tr}}
        </button>
        <button type="button" class="cancel notext" onclick="deleteDiag(this.form, 'DP')">{{tr}}Delete{{/tr}}</button>
        <script>
          CIM10Selector.initDP = function() {
            this.sForm     = "editDP";
            this.sView     = "DP";
            this.sChir     = "praticien_id";
            this.pop();
          }
        </script>
      </form>
    </td>
    
		<!-- Diagnostic Relié -->
    <td class="button">
      <form name="editDR" action="?" method="post" onsubmit="return onSubmitDiag(this);">
	      <input type="hidden" name="m" value="planningOp" />
	      <input type="hidden" name="dosql" value="do_sejour_aed" />
	      <input type="hidden" name="del" value="0" />
        {{mb_key object=$sejour}}
	      <input type="hidden" name="praticien_id" value="{{$sejour->praticien_id}}" />
	      <input type="hidden" name="DR" value='' onchange="this.form.onsubmit();"/>
	      <input type="text"   name="keywords_code" value="{{$sejour->DR}}" class="autocomplete str code cim10" size="10" />
	      <button type="button" class="search notext" onclick="CIM10Selector.initDR()">
        {{tr}}button-CCodeCIM10-choix{{/tr}}
      </button>
      <button type="button" class="cancel notext" onclick="deleteDiag(this.form, 'DR')">{{tr}}Delete{{/tr}}</button>
      <script>
        CIM10Selector.initDR = function() {
            this.sForm     = "editDR";
            this.sView     = "DR";
            this.sChir     = "praticien_id";
            this.pop();
        }
      </script>
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
        <input type="hidden" name="m" value="patients" />
        <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="object_class" value="CSejour" />
        <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
        <input type="hidden" name="_praticien_id" value="{{$sejour->praticien_id}}" />
        <input type="hidden" name="_added_code_cim" onchange="this.form.onsubmit();"/>
        <input type="text"   name="keywords_code" size="5" class="autocomplete str code cim10" />
        <button class="search notext" type="button" onclick="CIM10Selector.initAsso()">
          Chercher un diagnostic
        </button>
        <script>
          CIM10Selector.initAsso = function() {
            this.sForm = "editDA";
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
        <input type="hidden" name="m" value="patients" />
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
