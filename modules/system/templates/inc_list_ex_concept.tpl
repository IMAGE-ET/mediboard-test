<button type="button" class="new" onclick="ExConcept.edit('0',  null, 'exClassEditor')">
  Nouveau concept
</button>

<table class="main tbl">
  <tr>
    <th class="title" colspan="1">Concepts</th>
  </tr>
  {{foreach from=$list_ex_concept item=_ex_concept}}
    <tr>
      <td>
        <a href="#1" onclick="ExConcept.edit({{$_ex_concept->_id}}, null, 'exClassEditor')">
          {{$_ex_concept->_locale}}
        </a>
      </td
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="1">{{tr}}CExClassField.concept.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>