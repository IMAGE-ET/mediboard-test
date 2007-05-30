<form action="index.php?m={{$m}}" name="modlang" method="get">
<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />
<table class="main">
  <tr>
    <th>
      Traduction
      <select name="module" onchange="this.form.submit()">
      {{foreach from=$modules item=curr_module}}
      <option value="{{$curr_module}}" {{if $curr_module == $module}} selected="selected" {{/if}}>
        {{$curr_module}}
      </option>
      {{/foreach}}
      </select>
    </th>
  </tr>
</table>
</form>

<form action="index.php?m={{$m}}" name="translate" method="post">
<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />
<input type="hidden" name="module" value="{{$module}}" />
<input type="hidden" name="dosql" value="do_translate_aed" />
<table class="form">
<tr>
    <td>
      <table class="tbl">
        <tr>
          <th>Nom</th>
          <th>Chaine</th>
          <th>fr</th>
          <th class="category">
     		 <button type="submit" class="modify notext">{{tr}}Save{{/tr}}</button>
    	  </th>
        </tr>
        {{foreach from=$backSpecs key=key item=_item}}
        {{foreach from=$_item key=nom item=tabTrad}}
        <tr>
        	<td rowspan="{{$tabTrad|@count}}"> {{$nom}} </td>
        	{{foreach from=$tabTrad key=chaine item=trad}}
        	<td> {{$chaine}} </td>
        	<td><input size="40" type="text" name="tableau[{{$chaine}}]" value="{{$trad}}" /></td>
        	
        </tr>
        {{/foreach}}
        {{/foreach}}	
        {{/foreach}}
       </table>
    </td>
  </tr>
</table>
</form>