<form name="editFormGrossesse" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="maternite"/>
  <input type="hidden" name="dosql" value="do_grossesse_aed" />
  <input type="hidden" name="callback" value="Grossesse.afterEditGrossesse" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$grossesse}}
  {{mb_field object=$grossesse field=parturiente_id hidden=true}}
  
  <table class="form">
    <tr>
      {{mb_include module=system template=inc_form_table_header object=$grossesse}}
    </tr>
    <tr>
      <th>
        {{mb_label object=$grossesse field=terme_prevu}}
      </th>
      <td>
        {{mb_field object=$grossesse field=terme_prevu form=editFormGrossesse register=true}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$grossesse field=date_dernieres_regles}}
      </th>
      <td>
        {{mb_field object=$grossesse field=date_dernieres_regles form=editFormGrossesse register=true}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$grossesse field=active}}
      </th>
      <td>
        {{mb_field object=$grossesse field=active}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$grossesse field=multiple}}
      </th>
      <td>
        {{mb_field object=$grossesse field=multiple}}
      </td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        {{if $grossesse->_id}}
          <button type="button" class="save" onclick="this.form.onsubmit()">{{tr}}Save{{/tr}}</button>
          <button type="button" class="cancel"
            onclick="confirmDeletion(this.form, {objName: '{{$grossesse}}', ajax: 1})">{{tr}}Delete{{/tr}}</button>
        {{else}}
          <button type="button" class="save" onclick="this.form.onsubmit()">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>
