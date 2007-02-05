<table class="form">
  {{foreach from=$consult->_types_examen key=curr_type item=list_exams}}
  {{if $list_exams|@count}}
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
            <input type="hidden" name="exam_id" value="{{$curr_examcomp->exam_id}}" />
            <input type="hidden" name="fait" value="{{$curr_examcomp->fait}}" />
            <button class="trash notext" type="button" onclick="delExamComp(this.form)">
            </button>
            {{$curr_examcomp->examen}}
            {{if !$curr_examcomp->fait}}
              <button class="tick" type="button" onclick="modifEtatExamComp(this.form);">Fait</button>
            {{else}}
              <button class="cancel" type="button" onclick="modifEtatExamComp(this.form);">Annuler</button>
            {{/if}}
            </form>
          </li>
          {{/foreach}}
        </ul>
      </td>
    </tr>
  {{/if}}
  {{foreachelse}}
  <tr>
    <td>
      Pas d'examen complémentaire
    </td>
  </tr>
  {{/foreach}}
</table>