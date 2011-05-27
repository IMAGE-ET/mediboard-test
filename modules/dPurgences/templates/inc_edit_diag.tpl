<script type="text/javascript">
  Main.add(function() {
    var form = getForm("editDiag");
    var options = {
      objectClass: "{{$rpu->_class_name}}",
      contextUserId: "{{$user->_id}}",
      contextUserView: "{{$user->_view}}",
      timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
      validate: function() { form.onsubmit(); },
      resetSearchField: false,
      resetDependFields: false,
      validateOnBlur: false,
      height: "100px"
    };
    new AideSaisie.AutoComplete(form.elements.diag_infirmier, options);
  });
</script>
<form name="editDiag" method="post" action="?" onsubmit="return onSubmitFormAjax(this);">
  <input type="hidden" name="m" value="dPurgences" />
  <input type="hidden" name="dosql" value="do_rpu_aed" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$rpu}}
  <table class="form">
    <tr>
      <th class="category" colspan="2">{{tr}}CRPU{{/tr}}</th>
    </tr>
    <tr>
      <th>{{mb_label object=$rpu field=diag_infirmier}}</th>
      <td>{{mb_field object=$rpu field=diag_infirmier onchange="this.form.onsubmit();" class="autocomplete"}}</th>
    </tr>
  </table>
</form>