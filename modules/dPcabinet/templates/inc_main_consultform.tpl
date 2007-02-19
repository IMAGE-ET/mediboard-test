<form class="watch" name="editFrmExams" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_consultation_aed" />
{{mb_field object=$consult field="consultation_id" type="hidden" spec=""}}
{{mb_field object=$consult field="_check_premiere" type="hidden" spec=""}}

<table class="form">
  <tr>
    <th class="category">
      <label for="motif" title="Motif de la consultation">Motif</label>
    </th>
    <th>
      <select name="_helpers_motif" size="1" onchange="pasteHelperContent(this);this.form.motif.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult->_aides.motif}}
      </select>
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultation', this.form.motif)"/>
    </th>
    <th class="category">
      <label for="rques" title="Remarques concernant la consultation">Remarques</label>
    </th>
    <th>
      <select name="_helpers_rques" size="1" onchange="pasteHelperContent(this);this.form.rques.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult->_aides.rques}}
      </select>
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultation', this.form.rques)"/>
    </th>
  </tr>
  <tr>
    <td class="text" colspan="2">{{mb_field object=$consult field="motif" rows="5" onchange="submitFormAjax(this.form, 'systemMsg');"}}</td>
    <td class="text" colspan="2">{{mb_field object=$consult field="rques" rows="5" onchange="submitFormAjax(this.form, 'systemMsg');"}}</td>
  </tr>
  <tr>
    <th class="category">
      <label for="examen" title="Bilan de l'examen clinique">Examens</label>
    </th>
    <th>
      <select name="_helpers_examen" size="1" onchange="pasteHelperContent(this);this.form.examen.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult->_aides.examen}}
      </select>
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultation', this.form.examen)"/>
    </th>
    <th class="category">
      <label for="traitement" title="title">Traitements</label>
    </th>
    <th>
      <select name="_helpers_traitement" size="1" onchange="pasteHelperContent(this);this.form.traitement.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult->_aides.traitement}}
      </select>
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultation', this.form.traitement)"/>
    </th>
  </tr>
  <tr>
    <td class="text" colspan="2">{{mb_field object=$consult field="examen" rows="5" onchange="submitFormAjax(this.form, 'systemMsg');"}}</td>
    <td class="text" colspan="2">{{mb_field object=$consult field="traitement" rows="5" onchange="submitFormAjax(this.form, 'systemMsg');"}}</td>
  </tr>
  <tr>
    <td class="button" colspan="4">
      <button class="modify" type="button" onclick="submitFormAjax(this.form, 'systemMsg')">
        sauver
      </button>
    </td>
  </tr>
</table>
</form>