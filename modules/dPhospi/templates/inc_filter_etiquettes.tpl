
<button type="button" onclick="ModeleEtiquette.edit(''); removeSelected();" class="new">
  {{tr}}CModeleEtiquette.new{{/tr}}
</button>

<!--  Filtre -->
<table class="form">
  <tr>
    <th class="title" colspan="2">
      {{tr}}CModeleEtiquette.filter{{/tr}}
    </th>
  </tr>
  <tr>
    <td colspan="2">
      <form name="Filter" method="get" action="?" onsubmit="return ModeleEtiquette.refreshList()">
        <!--  Par object class -->
        <select name="filter_class">
          <option value="all">&mdash; Tous les types d'objets</option>
          {{foreach from=$classes key=_class item=_class_tr}}
            <option value="{{$_class}}" {{if $_class == $filter_class}} selected="selected" {{/if}}>
              {{tr}}{{$_class}}{{/tr}}
            </option>
          {{/foreach}}
        </select>
        <button class="search" type="submit">{{tr}}Filter{{/tr}}</button>
      </form>
    </td>
  </tr>
</table>