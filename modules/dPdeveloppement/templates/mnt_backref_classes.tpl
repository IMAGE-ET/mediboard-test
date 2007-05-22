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
          <th colspan="3">Fonction getBackRefs()</th>
          <th colspan="0">Classes de références</th>
        </tr>
        <tr>
          <th>Alerte</th>
          <th>Attribut</th>
          <th>Nom</th>
          <th>Nom</th>
        </tr>
        {{assign var="styleColorKey"     value="style=\"background-color:#afa;\""}}
        {{assign var="styleColorError"   value="style=\"background-color:#f00;\""}}
        {{assign var="styleColorConflit" value="style=\"background-color:#fc0;\""}}
        
        {{foreach from=$tab key=keyTab item=_itemTab}}
       	<tr>
          <th colspan="0" class="title">
           	<button id="Suggestion-{{$keyTab}}-trigger" class="edit" style="float: right">
              {{tr}}Suggestion{{/tr}}
            </button>
            {{$keyTab}} ({{tr}}{{$keyTab}}{{/tr}}) 
          </th>
        </tr>
        <tr id="Suggestion-{{$keyTab}}">
          <td colspan="0">
            <script type="text/javascript">new PairEffect('Suggestion-{{$keyTab}}');</script>
            <pre>{{tr}}{{$tabSuggestions.$keyTab|default:"no suggestion"}}{{/tr}}</pre>
          </td>
        </tr>
        {{foreach from=$_itemTab key=keyItemTab item=_item}}
       	<tr>
       	  {{if @$_item.real}}
          	  {{if $_item.real.condition == "okn"}}
                <td {{$styleColorConflit|smarty:nodefaults}}>
                	Aucun nom n'a été défini.
                </td>
              {{else}}
              	 <td {{if $_item.real.condition == "ok"}}
              	 		{{$styleColorKey|smarty:nodefaults}}
              	 	 {{else}}
                		{{$styleColorError|smarty:nodefaults}}
              		{{/if}} >
              	 </td>
              {{/if}}
          <td {{if $_item.real.condition == "r"}}
              	 {{$styleColorError|smarty:nodefaults}}
              {{else}}
                 {{$styleColorKey|smarty:nodefaults}}
              {{/if}} >
            	{{$_item.real.attribut}}
		  </td>
          <td {{if $_item.real.condition == "r"}}
              	 {{$styleColorError|smarty:nodefaults}}
              {{else}}
                 {{$styleColorKey|smarty:nodefaults}}
              {{/if}} >
            	{{$keyItemTab}}
          </td>
          <td {{if $_item.real.condition == "r"}}
              	 {{$styleColorError|smarty:nodefaults}}
              {{else}}
                 {{$styleColorKey|smarty:nodefaults}}
              {{/if}} >
              	{{$keyItemTab}}
          </td>
          {{else}}
          <td {{if $_item.theo == "ok"}}
              	 {{$styleColorKey|smarty:nodefaults}}
              {{elseif $_item.theo == ""}}
                 {{$styleColorConflit|smarty:nodefaults}}
              {{else}}
                 {{$styleColorError|smarty:nodefaults}}
              {{/if}} >
          </td>
          <td {{if $_item.theo == "ok"}}
              	 {{$styleColorKey|smarty:nodefaults}}
              {{elseif $_item.theo == ""}}
                 {{$styleColorConflit|smarty:nodefaults}}
              {{else}}
                 {{$styleColorError|smarty:nodefaults}}
              {{/if}} >
          </td>
          <td {{if $_item.theo == "ok"}}
              	 {{$styleColorKey|smarty:nodefaults}}
              {{elseif $_item.theo == ""}}
                 {{$styleColorConflit|smarty:nodefaults}}
              {{else}}
                 {{$styleColorError|smarty:nodefaults}}
              {{/if}} >
          </td>
          <td {{if $_item.theo == "ok"}}
              	 {{$styleColorKey|smarty:nodefaults}}
              {{elseif $_item.theo == ""}}
                 {{$styleColorConflit|smarty:nodefaults}}
              {{else}}
                 {{$styleColorError|smarty:nodefaults}}
              {{/if}} >
            	{{$keyItemTab}}
          </td>
          {{/if}}
        </tr>
        {{/foreach}}
        {{/foreach}}
       </table>
    </td>
  </tr>
</table>