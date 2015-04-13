{{mb_script module=urgences script=motif}}
{{mb_default var=readonly value=0}}

{{assign var=object value=""}}
{{if !$readonly}}
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
        {{mb_include module=system template=inc_form_table_header object=$motif colspan="3"}}
        <tr>
          <th>{{mb_label object=$motif field=chapitre_id}}</th>
          <td colspan="2">
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
          <td colspan="2">{{mb_field object=$motif field=nom}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$motif field=code_diag}}</th>
          <td colspan="2">{{mb_field object=$motif field=code_diag}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$motif field=degre_min}}</th>
          <td colspan="2">{{mb_field object=$motif field=degre_min increment=true form="Edit-CMotif"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$motif field=degre_max}}</th>
          <td colspan="2">{{mb_field object=$motif field=degre_max increment=true form="Edit-CMotif"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$motif field=definition}}</th>
          <td colspan="2">{{mb_field object=$motif field=definition}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$motif field=observations}}</th>
          <td colspan="2">{{mb_field object=$motif field=observations}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$motif field=param_vitaux}}</th>
          <td colspan="2">{{mb_field object=$motif field=param_vitaux}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$motif field=recommande}}</th>
          <td colspan="2">{{mb_field object=$motif field=recommande}}</td>
        </tr>

        <tbody id="questions">
          <tr>
            <th class="title" colspan="3">
              {{tr}}CMotifQuestion.all{{/tr}}
              <button class="add" type="button" onclick="Question.edit(0, '{{$motif->_id}}');" style="float: left;margin-right: -116px;">{{tr}}CMotifQuestion-msg-create{{/tr}}</button>
            </th>
          </tr>
          <tr>
            <th class="category narrow">{{mb_label class=CMotifQuestion field="degre"}}</th>
            <th class="category">{{mb_label class=CMotifQuestion field="nom"}}</th>
            <th class="category narrow">{{tr}}Action{{/tr}}</th>
          </tr>

        {{foreach from=$motif->_ref_questions item=_question}}
          <tr>
            <td>{{mb_value object=$_question field="degre"}}</td>
            <td>{{mb_value object=$_question field="nom"}}</td>
            <td>
              <button class="edit notext" type="button" onclick="Question.edit('{{$_question->_id}}');">Modifier</button>
              <button class="trash notext" type="button" onclick="Question.remove('{{$_question->_id}}', '{{$_question->nom}}');">
                {{tr}}Delete{{/tr}}
              </button>
            </td>
          </tr>

        {{foreachelse}}
          <tr>
            <td colspan="10" class="empty">{{tr}}CMotifQuestion.none{{/tr}}</td>
          </tr>
        {{/foreach}}
        </tbody>
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

  {{if !$chapitre_id && $chapitre_id != '0'}}
    <form name="question-delete" action="?m={{$m}}" method="post" onsubmit="return Question.onSubmit(this);">
      {{mb_class  object=$question}}
      {{mb_key    object=$question}}
      <input type="hidden" name="motif_id" value="{{$motif->_id}}"/>
      <input type="hidden" name="del" value="1"/>
    </form>
  {{/if}}

{{else}}

  <table class="tbl">
    <tr>
      <th class="title">
        {{$motif->code_diag}} {{$motif->nom}}
        <br/>
        Degrés:
        {{if $motif->degre_min <= 1 && $motif->degre_max >=1 }}[1]{{/if}}
        {{if $motif->degre_min <= 2 && $motif->degre_max >=2 }}[2]{{/if}}
        {{if $motif->degre_min <= 3 && $motif->degre_max >=3 }}[3]{{/if}}
        {{if $motif->degre_min <= 4 && $motif->degre_max >=4 }}[4]{{/if}}
      </th>
    </tr>
    <tr>
      <th>{{mb_label object=$motif field=definition}}</th>
    </tr>
    <tr>
      <td>{{mb_value object=$motif field=definition}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$motif field=observations}}</th>
    </tr>
    <tr>
      <td>{{mb_value object=$motif field=observations}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$motif field=param_vitaux}}</th>
    </tr>
    <tr>
      <td>{{mb_value object=$motif field=param_vitaux}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$motif field=recommande}}</th>
    </tr>
    <tr>
      <td>{{mb_value object=$motif field=recommande}}</td>
    </tr>
    <tr>
      <th>Questions</th>
    </tr>
    <tr>
      <td {{if !$motif->_ref_questions_by_degre|@count}}class="empty" {{/if}}>
        {{foreach from=$motif->_ref_questions_by_degre key=degre item=questions}}
          <strong>Degré {{$degre}}:</strong>
          <ul>
            {{foreach from=$questions item=_question}}
              <li>{{$_question->nom}}</li>
            {{/foreach}}
          </ul><br/>
        {{foreachelse}}
          {{tr}}CMotifQuestion.none{{/tr}}
        {{/foreach}}
      </td>
    </tr>
  </table>
{{/if}}