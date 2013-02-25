{{mb_script module=urgences script=motif}}
{{assign var=objetc value=""}}

{{if $chapitre_id || $chapitre_id == '0'}}
  {{assign var=object value=$chapitre}}
  <form name="Edit-CChapitreMotif" action="?" method="post" onsubmit="return Chapitre.onSubmit(this);">
    {{mb_class  object=$chapitre}}
    {{mb_key    object=$chapitre}}
    
    <table class="form">
      {{mb_include module=system template=inc_form_table_header object=$chapitre}}
      <tr>
        <th>{{mb_label object=$chapitre field=nom}}</th>
        <td>{{mb_field object=$chapitre field=nom}}</td>
      </tr>
{{else}}
  {{assign var=object value=$motif}}
  <form name="Edit-CMotif" action="?" method="post" onsubmit="return Motif.onSubmit(this);">
    {{mb_class  object=$motif}}
    {{mb_key    object=$motif}}
    
    <table class="form">
      {{mb_include module=system template=inc_form_table_header object=$motif}}
      <tr>
        <th>{{mb_label object=$motif field=chapitre_id}}</th>
        <td>
          <select name="chapitre_id" style="width: 120px;">
            <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
            {{foreach from=$chapitres item=chap}}
              <option value="{{$chap->_id}}"
                {{if $chap->_id == $motif->chapitre_id}}selected="selected"{{/if}}>
                {{$chap->nom}}
              </option>
            {{/foreach}}
          </select>
        </td>
      </tr>
      <tr>
        <th>{{mb_label object=$motif field=nom}}</th>
        <td>{{mb_field object=$motif field=nom}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$motif field=code_diag}}</th>
        <td>{{mb_field object=$motif field=code_diag}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$motif field=degre_min}}</th>
        <td>{{mb_field object=$motif field=degre_min increment=true form="Edit-CMotif"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$motif field=degre_max}}</th>
        <td>{{mb_field object=$motif field=degre_max increment=true form="Edit-CMotif"}}</td>
      </tr>
{{/if}}

    <tr>
      <td class="button" colspan="2">
        {{if $object->_id}}
          <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>
          <button class="trash" type="reset" onclick="return Chapitre.confirmDeletion(this.form);">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
          <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>