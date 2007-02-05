      <script>
      function reloadAddictions() {
        var antUrl = new Url;
        antUrl.setModuleAction("dPcabinet", "httpreq_vw_list_addictions");
        antUrl.addParam("consultation_anesth_id", "{{$consult_anesth->consultation_anesth_id}}");
        antUrl.requestUpdate('listAddict', { waitingText : null});
      }
      
      function submitAddiction(oForm){
        submitFormAjax(oForm, 'systemMsg', { onComplete : reloadAddictions });
      }
      
      function finAddiction(oForm){
        oForm._hidden_addiction.value = oForm.addiction.value;
        oForm.addiction.value = "";
        oForm._helpers_addiction.value = "";
      }
      </script>
      
      <hr />
      <form name="editTabacFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
      <input type="hidden" name="consultation_anesth_id" value="{{$consult_anesth->consultation_anesth_id}}" />
      <input type="hidden" name="listCim10" value="{{$consult_anesth->listCim10}}" />
      </form>

      <form name="editAddictFrm" action="?m=dPcabinet" method="post">
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_addiction_aed" />
      <input type="hidden" name="object_id" value="{{$consult_anesth->consultation_anesth_id}}" />
      <input type="hidden" name="object_class" value="CConsultAnesth" />      
      <table class="form">

        <tr>
          <td colspan="2"><strong>Addiction</strong></td>
          <td>
            <label for="addiction" title="Information sur l'addiction">Information</label>
            <select name="_helpers_addiction" size="1" onchange="pasteHelperContent(this)">
              <option value="">&mdash; Choisir une aide</option>
              {{html_options options=$addiction->_aides.addiction}}
            </select>
            <input type="hidden" name="_hidden_addiction" value="" />
            <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CAddiction', this.form._hidden_addiction, 'addiction')"/>
          </td>
        </tr>
        
        <tr>
          <th><label for="type" title="Type d'addiction">Type</label></th>
          <td>
            {{html_options name="type" options=$addiction->_enumsTrans.type}}
          </td>
          <td>
            <textarea name="addiction" onblur="if(verifNonEmpty(this)){submitAddiction(this.form);finAddiction(this.form);}"></textarea>
          </td>
        </tr>
        <tr>
          <td class="button" colspan="3">
            <button class="submit" type="button" onclick="if(verifNonEmpty(this.form.addiction)){submitAddiction(this.form);finAddiction(this.form);}">Ajouter</button>
          </td>
        </tr>
      </table>
      </form>