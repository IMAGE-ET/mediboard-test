<form name="edit-constantes-medicales" action="?" method="post" onsubmit="return checkForm(this);">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_constantes_medicales_aed" />
  {{if !$constantes->datetime}}
  <input type="hidden" name="datetime" value="now" />
  <input type="hidden" name="_new_constantes_medicales" value="1" />
  {{else}}
  <input type="hidden" name="constantes_medicales_id" value="{{$constantes->_id}}" />
  <input type="hidden" name="_new_constantes_medicales" value="0" />
  {{/if}}
  {{mb_field object=$constantes field=context_class hidden=1}}
  {{mb_field object=$constantes field=context_id hidden=1}}
  {{mb_field object=$constantes field=patient_id hidden=1}}
  
  <table class="tbl" style="width: 1%;">
    <tr>
      <th>{{mb_label object=$constantes field=ta}}</th>
      <th>{{mb_label object=$constantes field=pouls}}</th>
      <th>{{mb_label object=$constantes field=spo2}}</th>
      <th>{{mb_label object=$constantes field=temperature}}</th>
      {{if $constantes->datetime}}
      <th>{{mb_label object=$constantes field=datetime}}</th>
      {{/if}}
      <th></th>
    </tr>
    <tr>
      <td>
        {{mb_field object=$constantes field=_ta_systole size="1"}} /
        {{mb_field object=$constantes field=_ta_diastole size="1"}} cm Hg
      </td>
      <td>{{mb_field object=$constantes field=pouls size="4"}} /min</td>
      <td>{{mb_field object=$constantes field=spo2 size="4"}} %</td>
      <td>{{mb_field object=$constantes field=temperature size="4"}} °C</td>
      {{if $constantes->datetime}}
      <td>{{mb_field object=$constantes field=datetime form="edit-constantes-medicales" register=true}}</td>
      {{/if}}
      <td>
        <button class="modify" onclick="return submitConstantesMedicales(this.form);">
          {{if !$constantes->datetime}}
            {{tr}}Create{{/tr}}
          {{else}}
            {{tr}}Modify{{/tr}}
          {{/if}}
        </button>

        {{if $constantes->datetime}}
          <br />
          <button class="new" onclick="$V(this.form.constantes_medicales_id, null); $V(this.form._new_constantes_medicales, 1); return submitConstantesMedicales(this.form);">
            {{tr}}Create{{/tr}}
          </button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>