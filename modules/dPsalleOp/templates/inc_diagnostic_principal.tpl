{{mb_include_script module="dPplanningOp" script="cim10_selector"}}

<table class="form">
  <tr>
    <td class="button">{{mb_label object=$sejour field="DP"}}</td>
    {{if $modeDAS}}
    <td class="button">Diagnostics associés({{$sejour->_ref_dossier_medical->_ext_codes_cim|@count}})</td>
    {{/if}}
  </tr>
  <tr>
    <td class="button">

      <form name="editSejour" action="?m={{$m}}" method="post">

      <input type="hidden" name="m" value="dPplanningOp" />
      <input type="hidden" name="dosql" value="do_sejour_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
      <input type="hidden" name="praticien_id" value="{{$sejour->praticien_id}}" />
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
      </script>
      <input type="text" name="DP" value="{{$sejour->DP}}" class="code cim10" size="10"
        onchange="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { reloadDiagnostic({{$sejour->_id}}, {{$modeDAS}}) } })" />
      <button class="tick" type="button">{{tr}}Valider{{/tr}}</button>
      </form>
    </td>
    {{if $modeDAS}}
    <td class="text" rowspan="2">
      <form name="editDossierMedical" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="object_class" value="CSejour" />
        <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
        <input type="hidden" name="_praticien_id" value="{{$sejour->praticien_id}}" />
        <button class="search notext" type="button" onclick="CIM10Selector.initAsso()">
          Chercher un diagnostic
        </button>
        <input type="text" name="_added_code_cim" size="5" onchange="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { reloadDiagnostic({{$sejour->_id}}, {{$modeDAS}}) } })" />
        <button class="tick notext" type="button">
          Valider
        </button>
        <script type="text/javascript">   
          CIM10Selector.initAsso = function(){
            this.sForm = "editDossierMedical";
            this.sView = "_added_code_cim";
            this.sChir = "_praticien_id";
            this.selfClose = false;
            this.pop();
          }
        </script> 
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
            <button class="trash notext" type="button" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { reloadDiagnostic({{$sejour->_id}}, {{$modeDAS}}) } })">
              {{tr}}Delete{{/tr}}
            </button>
          </form>
          {{$curr_cim->code}} : {{$curr_cim->libelle}}
          <br />
      {{/foreach}}
    </td>
    {{/if}}
  </tr>
  <tr>
    <td class="text button">
      {{if $sejour->_ext_diagnostic_principal}}
      {{$sejour->_ext_diagnostic_principal->libelle}}
      {{/if}}
    </td>
  </tr>
</table>
<script>
CIM10Selector.set = function(code){
  var oForm = document[this.sForm];
  oForm[this.sView].value = code;
  oForm[this.sView].onchange();
}
</script>
<hr />