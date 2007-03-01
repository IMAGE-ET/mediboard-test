      <hr />
      <form name="editTabacFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
      {{mb_field object=$consult_anesth field="consultation_anesth_id" hidden=1 prop=""}}
      {{mb_field object=$consult_anesth field="listCim10" hidden=1 prop=""}}
      <table class="form">
      <tr>
        <td>
          {{mb_label object=$consult_anesth field="tabac"}}
          <select name="_helpers_tabac" size="1" onchange="pasteHelperContent(this);this.form.tabac.onchange();">
            <option value="">&mdash; Choisir une aide</option>
            {{html_options options=$consult_anesth->_aides.tabac}}
          </select>
          <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultAnesth', this.form.tabac)"/>
        </td>
        <td>
          {{mb_label object=$consult_anesth field="oenolisme"}}
          <select name="_helpers_oenolisme" size="1" onchange="pasteHelperContent(this);this.form.oenolisme.onchange();">
            <option value="">&mdash; Choisir une aide</option>
            {{html_options options=$consult_anesth->_aides.oenolisme}}
          </select>
          <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultAnesth', this.form.oenolisme)"/>
        </td>
      </tr>
      <tr>  
        <td>
          {{mb_field object=$consult_anesth field="tabac" onchange="submitForm(this.form);"}}
        </td>
        <td>
          {{mb_field object=$consult_anesth field="oenolisme" onchange="submitForm(this.form);"}}
        </td>
      </tr>
      </table>
      </form>