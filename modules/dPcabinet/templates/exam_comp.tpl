<table class="form">
  {{foreach from=$consult->_types_examen key=_type item=_examens}}
    <tr>
      <th class="category" colspan="3">
        {{tr}}CExamComp.realisation.{{$_type}}{{/tr}}
      </th>
    </tr>
    {{foreach from=$_examens item=_examen}}
      <tr>
        <td class="narrow">
          <form name="Del-{{$_examen->_guid}}" action="?m=dPcabinet" method="post">
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_examcomp_aed" />
            {{mb_key object=$_examen}}
            {{mb_field object=$_examen field=fait hidden=1}}
            
            <button class="trash notext" type="button" onclick="ExamComp.del(this.form)">
              {{tr}}Delete{{/tr}}
            </button>
          </form>
        </td>
        <td class="text">{{$_examen}}</td>
        <td class="narrow">
          {{if !$_examen->fait}}
            <button class="tick" type="button" onclick="ExamComp.toggle(getForm('Del-{{$_examen->_guid}}'));">{{tr}}Done{{/tr}}</button>
          {{else}}
            <button class="cancel notext" type="button" onclick="ExamComp.toggle(getForm('Del-{{$_examen->_guid}}'));">{{tr}}Cancel{{/tr}}</button>
          {{/if}}
        </td>
      </tr>
    {{/foreach}}
  {{/foreach}}
</table>