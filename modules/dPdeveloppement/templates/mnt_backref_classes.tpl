<table class="main">
  <tr>
    <th>
      <form action="index.php" name="mntBackRef" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />

      <label for="selClass" title="Veuillez Sélectionner une classe">Choix de la classe</label>
      <select class="notNull str" name="selClass" onchange="submit();">
        <option value=""{{if !$selClass}} selected="selected"{{/if}}>&mdash; Liste des erreurs</option>
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
          <th rowspan="1">Fonction getBackRefs()</th>
          <th rowspan="1">Classes de références</th>
        </tr>
        {{assign var="styleColorKey"     value="style=\"background-color:#afa;\""}}
        {{assign var="styleColorError"   value="style=\"background-color:#f00;\""}}
        
        {{foreach from=$backRefs key=keyRef item=_itemRef}}
       	<tr>
          <th colspan="0" class="title">
            {{$keyRef}} ({{tr}}{{$keyRef}}{{/tr}})
          </th>
        </tr>
        {{foreach from=$backSpecs key=keySpec item=_itemSpec}}
        {{foreach from=$_itemSpec item=curr_itemSpec}}
        {{foreach from=$_itemRef item=curr_itemRef}}
        {{if $curr_itemRef==$curr_itemSpec}}
        <tr>
          <td {{if $curr_itemRef==$curr_itemSpec}}
                {{$styleColorKey|smarty:nodefaults}}
              {{elseif $_itemRef|@count}}
                {{$styleColorError|smarty:nodefaults}}
              {{/if}}>
            {{$curr_itemRef}}
          </td>
        </tr>
        {{/if}}
        {{/foreach}}
        {{/foreach}}
        {{/foreach}}
        {{/foreach}}
       </table>
    </td>
  </tr>
</table>