<table class="layout">
  <tr>
    <td>
      {{mb_field object=$consult_anesth field=plus_de_55_ans typeEnum=checkbox onchange="verifIntubDifficileAndSave(this.form);"}}
      {{mb_label object=$consult_anesth field=plus_de_55_ans}}
    </td>
    <td>
      {{mb_field object=$consult_anesth field=edentation typeEnum=checkbox onchange="verifIntubDifficileAndSave(this.form);"}}
      {{mb_label object=$consult_anesth field=edentation}}
    </td>
    <td>
      {{mb_field object=$consult_anesth field=barbe typeEnum=checkbox onchange="verifIntubDifficileAndSave(this.form);"}}
      {{mb_label object=$consult_anesth field=barbe}}
    </td>
  </tr>
  <tr>
    <td>
      {{mb_field object=$consult_anesth field=imc_sup_26 typeEnum=checkbox onchange="verifIntubDifficileAndSave(this.form);"}}
      {{mb_label object=$consult_anesth field=imc_sup_26}}
    </td>
    <td>
      {{mb_field object=$consult_anesth field=ronflements typeEnum=checkbox onchange="verifIntubDifficileAndSave(this.form);"}}
      {{mb_label object=$consult_anesth field=ronflements}}
    </td>
    <td>
      {{mb_field object=$consult_anesth field=piercing typeEnum=checkbox onchange="verifIntubDifficileAndSave(this.form);"}}
      {{mb_label object=$consult_anesth field=piercing}}
    </td>
  </tr>
</table>