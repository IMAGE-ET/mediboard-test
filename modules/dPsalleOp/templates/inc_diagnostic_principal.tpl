{{mb_include_script module="dPplanningOp" script="cim10_selector"}}

<form name="editSejour" action="?m={{$m}}" method="post">

<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="dosql" value="do_sejour_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
<input type="hidden" name="praticien_id" value="{{$sejour->praticien_id}}" />

<table class="form">
  <tr>
    <th>{{mb_label object=$sejour field="DP"}}</th>
    <td>
      {{mb_field object=$sejour field="DP" size="10" onchange="submitFormAjax(this.form, 'systemMsg')"}}
      <button type="button" class="search" onclick="CIM10Selector.init()">
        {{tr}}button-CCodeCIM10-choix{{/tr}}
      </button>
      <script type="text/javascript">
        CIM10Selector.init = function(){
          this.sForm = "editSejour";
          this.sView = "DP";
          this.sChir = "praticien_id";
          this.pop();
        }
        CIM10Selector.set = function(code){
          var oForm = document[this.sForm];
          oForm[this.sView].value = code;
          submitFormAjax(oForm, 'systemMsg');
        }
      </script>
    </td>
  </tr>
</table>

</form>