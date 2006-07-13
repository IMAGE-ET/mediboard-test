<table class="form">
  <tr>
    <th class="category">
      <label for="motif" title="Motif de la consultation">Motif</label>
    </th>
    <th>
      <select name="_helpers_motif" size="1" onchange="pasteHelperContent(this)">
        <option value="0">&mdash; Choisir une aide</option>
        {{html_options options=$consult->_aides.motif}}
      </select>
    </th>
    <th class="category">
      <label for="rques" title="Remarques concernant la consultation">Remarques</label>
    </th>
    <th>
      <select name="_helpers_rques" size="1" onchange="pasteHelperContent(this)">
        <option value="0">&mdash; Choisir une aide</option>
        {{html_options options=$consult->_aides.rques}}
      </select>
    </th>
  </tr>
  <tr>
    <td class="text" colspan="2"><textarea name="motif" rows="5">{{$consult->motif}}</textarea></td>
    <td class="text" colspan="2"><textarea name="rques" rows="5">{{$consult->rques}}</textarea></td>
  </tr>
  <tr>
    <th class="category">
      <label for="examen" title="Bilan de l'examen clinique">Examens</label>
    </th>
    <th>
      <select name="_helpers_examen" size="1" onchange="pasteHelperContent(this)">
        <option value="0">&mdash; Choisir une aide</option>
        {{html_options options=$consult->_aides.examen}}
      </select>
    </th>
    <th class="category">
      <label for="traitement" title="title">Traitements</label>
    </th>
    <th>
      <select name="_helpers_traitement" size="1" onchange="pasteHelperContent(this)">
        <option value="0">&mdash; Choisir une aide</option>
        {{html_options options=$consult->_aides.traitement}}
      </select>
    </th>
  </tr>
  <tr>
    <td class="text" colspan="2"><textarea name="examen" rows="5">{{$consult->examen}}</textarea></td>
    <td class="text" colspan="2"><textarea name="traitement" rows="5">{{$consult->traitement}}</textarea></td>
  </tr>
  <tr>
    <td class="button" colspan="4">
      <button class="modify" type="button" onclick="submitFormAjax(this.form, 'systemMsg')">
        sauver
      </button>
    </td>
  </tr>
</table>