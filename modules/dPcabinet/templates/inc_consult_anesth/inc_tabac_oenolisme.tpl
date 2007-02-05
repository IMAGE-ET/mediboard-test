      <hr />
      <form name="editTabacFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
      <input type="hidden" name="consultation_anesth_id" value="{{$consult_anesth->consultation_anesth_id}}" />
      <input type="hidden" name="listCim10" value="{{$consult_anesth->listCim10}}" />
      <table class="form">
      <tr>
        <td>
          <label for="tabac" title="Comportement tabagique">Tabac</label>
          <select name="_helpers_tabac" size="1" onchange="pasteHelperContent(this);this.form.tabac.onchange();">
            <option value="">&mdash; Choisir une aide</option>
            {{html_options options=$consult_anesth->_aides.tabac}}
          </select>
          <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultAnesth', this.form.tabac)"/>
        </td>
        <td>
          <label for="oenolisme" title="Comportement alcoolique">Oenolisme</label>
          <select name="_helpers_oenolisme" size="1" onchange="pasteHelperContent(this);this.form.oenolisme.onchange();">
            <option value="">&mdash; Choisir une aide</option>
            {{html_options options=$consult_anesth->_aides.oenolisme}}
          </select>
          <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultAnesth', this.form.oenolisme)"/>
        </td>
      </tr>
      <tr>  
        <td>
          <textarea name="tabac" onchange="submitForm(this.form);">{{$consult_anesth->tabac}}</textarea>
        </td>
        <td>
          <textarea name="oenolisme" onchange="submitForm(this.form);">{{$consult_anesth->oenolisme}}</textarea>
        </td>
      </tr>
      </table>
      </form>