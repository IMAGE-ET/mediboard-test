<table class="main">
  <tr>
    <th>
      <form action="index.php" name="mntTable" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />

      <label for="selClass" title="Veuillez Sélectionner une Class">Choix du type d'objet</label>
      <select title="str|notNull" name="selClass" onchange="submit();">
        {{foreach from=$listClass item=curr_listClass}}
        <option value="{{$curr_listClass}}"{{if $selClass==$curr_listClass}} selected="selected"{{/if}}>{{tr}}{{$curr_listClass}}{{/tr}}</option>
        {{/foreach}}
      </select>
    </th>
  </tr>
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th rowspan="2">Objet</th>
          <th rowspan="2">Propriété de l'objet</th>
          <th colspan="5">Base de Données</th>
        </tr>
        <tr>
          <th>Nom</th>
          <th>Type</th>
          <th>Null</th>
          <th>Default</th>
          <th>Index</th>
        </tr>
        {{foreach from=$aChamps item=currChamp}}
        <tr>
          <td {{if !$currChamp.class_field}}style="background-color:#f00;"{{/if}}>
            {{$currChamp.class_field}}
          </td>
          <td {{if !$currChamp.class_props}}style="background-color:#f00;"{{/if}}>
            {{$currChamp.class_props}}
          </td>
          {{if !$currChamp.BDD_name}}
          <td colspan="5" style="background-color:#f00;">
          </td>
          {{else}}
          <td>
            {{$currChamp.BDD_name}}
          </td>
          <td>{{$currChamp.BDD_type}}</td>
          <td>
            {{if $currChamp.BDD_null!="NO"}}
            {{$currChamp.BDD_null}}
            {{/if}}
          </td>
          <td>{{$currChamp.BDD_default}}</td>
          <td>{{$currChamp.BDD_index}}</td>
          {{/if}}
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>