{{if $rpu->_ref_reponses|@count}}
  <fieldset>
    <legend>Questions définissant le degré</legend>
    <table>
      {{foreach from=$rpu->_ref_reponses item=_reponse}}
        <tr>
          <th style="text-align: left;">
            <strong>Degré {{$_reponse->_ref_question->degre}}:</strong> {{$_reponse->_ref_question->nom}}
          </th>
          <td>
            <form name="editReponse-{{$_reponse->_guid}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this);">
              {{mb_class  object=$_reponse}}
              {{mb_key    object=$_reponse}}
              <input type="hidden" name="rpu_id" value="{{$_reponse->rpu_id}}"/>
              <label>
                <input onclick="Motif.submitReponse(this.form);" type="radio" name="result"
                       {{if $_reponse->result}}checked="checked"{{/if}} value="1" /> {{tr}}Yes{{/tr}}
              </label>
              <label>
                <input onclick="Motif.submitReponse(this.form);" type="radio" name="result"
                       {{if $_reponse->result == "0"}}checked="checked"{{/if}} value="0" />{{tr}}No{{/tr}}
              </label>
            </form>
          </td>
        </tr>
        {{foreachelse}}
        <tr>
          <td colspan="2" class="empty">{{tr}}CMotifQuestion.none{{/tr}}</td>
        </tr>
      {{/foreach}}
    </table>
  </fieldset>
{{/if}}