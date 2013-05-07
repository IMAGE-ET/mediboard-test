{{mb_script module=facturation script=retrocession}}

<form name="select_prat" action="#" method="get">
  <input type="hidden" name="m"   value="dPfacturation">
  <input type="hidden" name="tab" value="vw_retrocession_regles">
  <table class="form main">
    <tr>
      <th>Praticien</th>
      <td>
         <select name="prat_id" onchange="this.form.submit();">
            {{if $listPrat|@count > 1}}
            <option value="">&mdash; Tous</option>
            {{/if}}
            {{mb_include module=mediusers template=inc_options_mediuser list=$listPrat selected=$praticien->_id}}
          </select>
      </td>
    </tr>
  </table>
</form>

{{if $praticien->_id}}
  <button type="button" class="new" onclick="Retrocession.edit('0');">{{tr}}CRetrocession-title-create{{/tr}}</button>
  <table name="retrocessions" class="main tbl">
    <tr>
      <th colspan="7" class="title">{{tr}}CRetrocession.all{{/tr}}</th>
    </tr>
    <tr>
      <th>{{mb_title class=CRetrocession field=nom}}</th>
      <th class="narrow">{{mb_title class=CRetrocession field=code_class}}</th>
      <th>{{mb_title class=CRetrocession field=code}}</th>
      <th>{{mb_title class=CRetrocession field=type}}</th>
      <th>{{mb_title class=CRetrocession field=valeur}}</th>
      {{if "tarmed"|module_active && $conf.tarmed.CCodeTarmed.use_cotation_tarmed}}
        <th>{{mb_title class=CRetrocession field=pct_pm}}</th>
        <th>{{mb_title class=CRetrocession field=pct_pt}}</th>
      {{/if}}
    </tr>
    {{foreach from=$praticien->_ref_retrocessions item=_retrocession}}
      <tr style="text-align:center;">
        <td><a href="#" onclick="Retrocession.edit('{{$_retrocession->_id}}');">{{mb_value object=$_retrocession field=nom}}</a></td>
        <td>{{mb_value object=$_retrocession field=code_class}}</td>
        <td>{{mb_value object=$_retrocession field=code}}</td>
        <td>{{mb_value object=$_retrocession field=type}}</td>
        {{if $_retrocession->type != "autre"}}
          {{if $_retrocession->type == "montant"}}
            <td>{{mb_value object=$_retrocession field=valeur}} {{$conf.currency_symbol}}</td>
          {{else}}
            <td>{{$_retrocession->valeur}} %</td>
          {{/if}}
        {{else}}
          <td></td>
        {{/if}}
        {{if "tarmed"|module_active && $conf.tarmed.CCodeTarmed.use_cotation_tarmed}}
          {{if $_retrocession->type == "autre"}}
            <td>{{mb_value object=$_retrocession field=pct_pm}}</td>
            <td>{{mb_value object=$_retrocession field=pct_pt}}</td>
          {{else}}
            <td></td>
            <td></td>
          {{/if}}
        {{/if}}
      </tr>
    {{foreachelse}}
      <tr>
        <td colspan="7" class="empty">{{tr}}CRetrocession.none{{/tr}}</td>
      </tr>
    {{/foreach}}
  </table>
{{/if}}