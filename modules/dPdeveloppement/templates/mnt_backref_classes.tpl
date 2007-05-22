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
          <th colspan="4">Fonction getBackRefs()</th>
          <th colspan="0">Classes de références</th>
        </tr>
        <tr>
          <th>Alerte</th>
          <th>Attribut</th>
          <th>Traduction</th>
          <th>Nom</th>
          <th>Nom</th>
        </tr>
      
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
          	  {{if $_item.real.condition == "warningNum"}}
                <td class="warning">
                	Aucun nom n'a été défini
                </td>
              {{else}}
              	 <td {{if $_item.real.condition == "ok"}}
              	 		class="ok"
              	 	 {{else}}
                		class="error"
              		{{/if}} >
              	 </td>
              {{/if}}
          <td {{if $_item.real.condition == "default" || $_item.real.condition == "noCMbObject"}}
              	 class="error"
              {{else}}
                 class="ok"
              {{/if}} >
            	{{$_item.real.attribut}} 
		  </td>
		  <td {{if $_item.real.condition == "default" || $_item.real.condition == "noCMbObject"}}
              	 class="error"
              {{else}}
                 class="ok"
              {{/if}} >
            	{{tr}}{{$keyTab}}-back-{{$_item.real.attribut}}{{/tr}}
		  </td> 
          <td {{if $_item.real.condition == "default" || $_item.real.condition == "noCMbObject"}}
              	 class="error"
              {{else}}
                 class="ok"
              {{/if}} >
            	{{$keyItemTab}}
          </td>
           <td {{if $_item.real.condition == "default" || $_item.real.condition == "noCMbObject"}}
              	 class="error"
              {{else}}
                 class="ok"
              {{/if}} >
              	{{$keyItemTab}}
          </td>
          {{else}}
          	  {{if $_item.theo == "okn"}}
                <td class="ok"> </td>
              {{elseif $_item.theo == ""}}
              	 <td class="warning"> 
              	 	Invérifiable
              	 </td>
              {{elseif $_item.theo == "noClass"}}
              	 <td class="error"> 
              	 	{{$_item.theo}}
              	 </td>
              {{else}}
                 <td class="error"> </td>
              {{/if}}
         <td {{if $_item.theo == "ok"}}
              	 class="ok"
              {{elseif $_item.theo == ""}}
                  class="warning"
              {{else}}
                 class="error"
              {{/if}} >
          </td>
          <td {{if $_item.theo == "ok"}}
              	 class="ok"
              {{elseif $_item.theo == ""}}
                  class="warning"
              {{else}}
                 class="error"
              {{/if}} >
          </td>
          <td {{if $_item.theo == "ok"}}
              	 class="ok"
              {{elseif $_item.theo == ""}}
                  class="warning"
              {{else}}
                 class="error"
              {{/if}} >
          </td>
          <td {{if $_item.theo == "ok"}}
              	 class="ok"
              {{elseif $_item.theo == ""}}
                  class="warning"
              {{else}}
                  class="error"
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