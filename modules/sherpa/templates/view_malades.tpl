<script language="JavaScript" type="text/javascript">

function affNaissance() {
  var oForm = document.find;
  var bNaissance = oForm.check_naissance.checked;
  oForm.naissance.value = bNaissance ? "on" : "off";
  Element.toggle(oForm.Date_Day, oForm.Date_Month, oForm.Date_Year);

}
</script>

<table class="main">
  <tr>
    <td class="halfPane">
    	{{include file="inc_filter_malades.tpl"}}
    </td>
    <td class="halfPane">
      {{include file="inc_view_malade.tpl"}}
    </td>
  </tr>
</table>