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
      <td>
        {{mb_field object=$rpu field=diag_infirmier onchange="this.form.onsubmit();" class="autocomplete" form="editDiag"
             aidesaisie="validate: function() { form.onsubmit();},
                         resetSearchField: 0,
                         resetDependFields: 0,
                         validateOnBlur: 0,
                         height: '100px'"}}
      </td>
    </tr>
  </table>
</form>