{{mb_include_script module="dPplanningOp" script="cim10_selector"}}

<form name="editSejour" action="?m={{$m}}" method="post">

<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="dosql" value="do_sejour_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
<input type="hidden" name="praticien_id" value="{{$sejour->praticien_id}}" />

<table class="form">
  <tr>
    <td class="button halfPane">{{mb_label object=$sejour field="DP"}}</td>
  </tr>
  <tr>
    <td class="button">
      <button type="button" class="search" onclick="CIM10Selector.init()">
        {{tr}}button-CCodeCIM10-choix{{/tr}}
      </button>
      <script type="text/javascript">
        CIM10Selector.init = function(){
          this.sForm     = "editSejour";
          this.sView     = "DP";
          this.sChir     = "praticien_id";
          this.selfClose = false;
          this.pop();
        }
        CIM10Selector.set = function(code){
          var oForm = document[this.sForm];
          oForm[this.sView].value = code;
          submitFormAjax(oForm, 'systemMsg', { onComplete: function() { reloadDiagnostic({{$sejour->_id}}) } });
        }
      </script>
      <input type="text" name="DP" value="{{$sejour->DP}}" class="code cim10" size="10"
        onchange="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { reloadDiagnostic({{$sejour->_id}}) } })" />
      <button class="tick" type="button">{{tr}}Valider{{/tr}}</button>
    </td>
  </tr>
  {{if $sejour->_ext_diagnostic_principal}}
  <tr>
    <td class="text button">
      {{$sejour->_ext_diagnostic_principal->libelle}}
    </td>
  </tr>
  {{/if}}
</table>
<hr />

</form>