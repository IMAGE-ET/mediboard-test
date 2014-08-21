
<form name="editConsultation" method="post" action="?" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="dosql" value="do_consultation_aed" />
  <input type="hidden" name="ajax" value="1" />
  {{mb_key object=$consult}}
  <fieldset>
      <legend>{{tr}}accident_travail{{/tr}}</legend>
      <table style="width: 100%;">
        <tr>
          <th style="width: 30%">{{mb_label object=$consult field=date_at}}</th>
          <td>{{mb_field object=$consult field=date_at form=editConsultation register=true onchange="this.form.onsubmit()"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$consult field=num_at}}</th>
          <td>
            {{mb_field object=$consult field=num_at style="width:50px;" size=8 onchange="this.form.onsubmit()"}}
            {{mb_field object=$consult field=cle_at onchange="this.form.onsubmit();" style="width:8px;"}}
            {{mb_label object=$consult field=cle_at}}
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$consult field=org_at}}</th>
          <td>{{mb_field object=$consult field=org_at onchange="this.form.onsubmit();" size=9}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$consult field=feuille_at}}</th>
          <td>{{mb_field object=$consult field=feuille_at onchange="this.form.onsubmit();"}}</td>
        </tr>
      </table>
    </fieldset>
</form>