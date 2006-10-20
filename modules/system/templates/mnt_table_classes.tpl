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
      </form>
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
        {{assign var="styleColorConflit" value="style=\"background-color:#fc0;\""}}
        
        {{foreach from=$aChamps key=keyChamp item=currChamp}}
        {{if $keytable==$currChamp.class_field && $currChamp.BDD_primary==true}}
          {{assign var="styleColor" value="style=\"background-color:#afa;\""}}
          {{assign var="colorTD" value="1"}}
        {{else}}
          {{assign var="colorTD" value="0"}}
          {{assign var="styleColor" value="style=\"background-color:#f00;\""}}
        {{/if}}
        <tr>
          <td {{if !$currChamp.class_field || $colorTD}}{{$styleColor|smarty:nodefaults}}{{/if}}>
            {{$currChamp.class_field}}
          </td>
          <td {{if !$currChamp.class_props || $colorTD}}{{$styleColor|smarty:nodefaults}}{{elseif $currChamp.error_class_props}}{{$styleColorConflit|smarty:nodefaults}}{{/if}}>
            {{$currChamp.class_props}}
            {{if !$currChamp.class_field && $currChamp.class_props}}
            &mdash; <strong>{{$keyChamp}}</strong>
            {{/if}}
          </td>
          {{if !$currChamp.BDD_name}}
          <td colspan="5" {{$styleColor|smarty:nodefaults}}>
          </td>
          {{else}}
          <td {{if $colorTD}}{{$styleColor|smarty:nodefaults}}{{/if}}>
            {{$currChamp.BDD_name}}
          </td>
          <td {{if $colorTD}}{{$styleColor|smarty:nodefaults}}{{elseif $currChamp.error_BDD_type}}{{$styleColorConflit|smarty:nodefaults}}{{/if}}>
            {{$currChamp.BDD_type}}
            {{if $currChamp.error_BDD_type}}
              &mdash; <strong>{{$currChamp.error_BDD_type}}</strong>
            {{/if}}
          </td>
          <td {{if $colorTD}}{{$styleColor|smarty:nodefaults}}{{elseif $currChamp.error_BDD_null}}{{$styleColorConflit|smarty:nodefaults}}{{/if}}>
            {{if $currChamp.BDD_null!="NO"}}
            {{$currChamp.BDD_null}}
            {{/if}}
          </td>
          <td {{if $colorTD}}{{$styleColor|smarty:nodefaults}}{{/if}}>
            {{$currChamp.BDD_default}}
          </td>
          <td {{if $colorTD}}{{$styleColor|smarty:nodefaults}}{{/if}}>
            {{$currChamp.BDD_index}}
          </td>
          {{/if}}
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>