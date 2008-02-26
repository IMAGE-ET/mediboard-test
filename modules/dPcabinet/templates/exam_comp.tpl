<table class="form">
  {{foreach from=$consult->_types_examen key=curr_type item=list_exams}}
  <tr>
    <th class="category">
      {{tr}}CExamComp.realisation.{{$curr_type}}{{/tr}}
    </th>
  </tr>  
  <tr>
    <td>
      <ul>
        {{foreach from=$list_exams item=curr_examcomp}}
        <li>
          <form name="delExamCompFrm{{$curr_examcomp->exam_id}}" action="?m=dPcabinet" method="post">
          <input type="hidden" name="m" value="dPcabinet" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="dosql" value="do_examcomp_aed" />
          {{mb_field object=$curr_examcomp field="exam_id" hidden=1 prop=""}}
          {{mb_field object=$curr_examcomp field="fait" hidden=1 prop=""}}
          <button class="trash notext" type="button" onclick="delExamComp(this.form)">
            {{tr}}Delete{{/tr}}
          </button>
          {{$curr_examcomp->examen}}
          {{if !$curr_examcomp->fait}}
            <button class="tick" type="button" onclick="modifEtatExamComp(this.form);">Fait</button>
          {{else}}
            <button class="cancel" type="button" onclick="modifEtatExamComp(this.form);">Annuler</button>
          {{/if}}
          </form>
        </li>
			  {{foreachelse}}
			  <li><em>{{tr}}CExamComp.none{{/tr}}</em></li>
        {{/foreach}}
      </ul>
    </td>
  </tr>
  {{/foreach}}
</table>