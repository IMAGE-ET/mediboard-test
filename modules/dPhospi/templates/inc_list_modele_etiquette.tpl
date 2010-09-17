<script type="text/css">
updateSelected = function(elem) {
  var etiq = $('modele_etiq-' + elem);
  etiq.siblings().each(function(cur) {
    cur.className = '';
  });
  etiq.className = 'selected';
}
</script>

<button type="button" onclick="editEtiq('');" class="new">{{tr}}CModeleEtiquette.new{{/tr}}</button>
<!--  Filtre -->
<table class="tbl">
  <tr>
    <th class="title" colspan="2">
      {{tr}}CModeleEtiquette.filter{{/tr}}
    </th>
  </tr>
  <tr>
    <td colspan="2">
      <form name="filter_etiq">
        <!--  Par object class -->
        <select name="filter_class">
          <option value="all">&mdash; Tous les types d'objets</option>
          {{foreach from=$classes|smarty:nodefaults key=_class item=_class_tr}}
            <option value="{{$_class}}" {{if $_class == $filter_class}} selected="selected" {{/if}}>
              {{tr}}{{$_class}}{{/tr}}
            </option>
          {{/foreach}}
        </select>
        <button class="search" type="button" onclick="refreshList($V(getForm('filter_etiq').filter_class))">{{tr}}Filter{{/tr}}</button>
      </form>
    </td>
  </tr>
  <!-- Liste des étiquettes filtrées -->
  <tr>
    <th class="title" colspan="2">
      {{tr}}CModeleEtiquette.list{{/tr}}
    </th>
  </tr>
  <tr>
    <th class="category">{{tr}}CModeleEtiquette-nom{{/tr}}</th>
    <th class="category">{{tr}}CModeleEtiquette-object_class{{/tr}}</th>
  </tr>
      
  {{foreach from=$liste_modele_etiquette item=_modele_etiq}}
    <tr id='modele_etiq-{{$_modele_etiq->_id}}' {{if $_modele_etiq->_id == $modele_etiquette_id}}class="selected"{{/if}}>
      <td>
       <a href="#1" onclick="editEtiq('{{$_modele_etiq->_id}}'); updateSelected('{{$_modele_etiq->_id}}');">
         <span onmouseover="ObjectTooltip.createEx(this, '{{$_modele_etiq->_guid}}')">
          {{mb_value object=$_modele_etiq field=nom}}
          </span>
        </a>
      </td>
      <td>
        {{tr}}{{$_modele_etiq->object_class}}{{/tr}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="2">
      {{tr}}CModeleEtiquette.none{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>