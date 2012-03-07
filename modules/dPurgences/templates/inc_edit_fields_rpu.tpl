{{if $access_pmsi || $is_praticien}}
  {{assign var=sejour value=$rpu->_ref_sejour}}
  <form name="editSejour" action="?" method="post">
    <input type="hidden" name="m" value="dPplanningOp"/>
    <input type="hidden" name="dosql" value="do_sejour_aed"/>
    <input type="hidden" name="del" value="0" />
    {{mb_key object=$sejour}}
    <table class="form">
      <th class="category" colspan="2">{{tr}}CSejour{{/tr}}</th>
      <tr>
        {{mb_include module=urgences template=inc_diagnostic_principal}}
      </tr>
    </table>
  </form>
{{/if}}

{{if $is_praticien || $can->admin}}
  <form name="editRPU" method="post" action="?" onsubmit="return onSubmitFormAjax(this);">
    <input type="hidden" name="m" value="dPurgences"/>
    <input type="hidden" name="dosql" value="do_rpu_aed"/>
    <input type="hidden" name="del" value="0" />
    {{mb_key object=$rpu}}
    <table class="form">
      <tr>
        <th class="category" colspan="2">{{tr}}CRPU{{/tr}}</th>
      </tr>
      <tr>
        <th>{{mb_label object=$rpu field="ccmu"}}</th>
        <td>{{mb_field object=$rpu field="ccmu" emptyLabel="Choose" onchange="this.form.onsubmit();"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$rpu field="gemsa"}}</th>
        <td>{{mb_field object=$rpu field="gemsa" canNull=false emptyLabel="Choose" onchange="this.form.onsubmit();"}}</td>
      </tr>
    </table>
  </form>
{{/if}}