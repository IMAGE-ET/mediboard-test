<table class="main">
  <tr>
    <th>
      <form action="?" name="mntTable" method="get">
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
          <th rowspan="2">Champ</th>
          <th rowspan="2">Propriété du champ</th>
          <th colspan="5">Base de Données</th>
        </tr>
        <tr>
          <th>Nom</th>
          <th>Type</th>
          <th>Null</th>
          <th>Default</th>
          <th>Index</th>
        </tr>
        
        {{foreach from=$aChamps key=keyClass item=currClass}}
        
        {{if $currClass|@count}}
        <tr>
          <th colspan="7" class="title">
            <button id="Suggestion-{{$keyClass}}-trigger" class="edit" style="float: right">
              {{tr}}Suggestion{{/tr}}
            </button>
            {{$keyClass}} ({{tr}}{{$keyClass}}{{/tr}})
          </th>
        </tr>
        <tr id="Suggestion-{{$keyClass}}">
          <td colspan="100">
            <script type="text/javascript">new PairEffect('Suggestion-{{$keyClass}}');</script>
  
            <pre>{{tr}}{{$aSuggestions.$keyClass|default:"no suggestion"}}{{/tr}}</pre>
          </td>
        </tr>
        {{foreach from=$currClass key=keyChamp item=currChamp}}
        <tr>
          <td {{if !$currChamp.class_field}}
                class="error"
              {{elseif $currChamp.keytable && $currChamp.keytable==$currChamp.class_field}}
                class="ok"
              {{/if}}>
            {{$currChamp.class_field}}
          </td>
          <td {{if $currChamp.keytable && $currChamp.keytable==$currChamp.class_field && $currChamp.class_props}}
                class="error"
              {{elseif $currChamp.keytable && $currChamp.keytable==$currChamp.class_field}}
                class="ok"
              {{elseif $currChamp.error_class_props}}
                class="warning"
              {{elseif !$currChamp.class_props}}
                class="error"
              {{/if}}>
            {{$currChamp.class_props}}
            {{if !$currChamp.class_field && $currChamp.class_props}}
            &mdash; <strong>{{$keyChamp}}</strong>
            {{/if}}
          </td>
          {{if !$currChamp.BDD_name}}
          <td colspan="5" class="error">
          {{if $currChamp.object_spec}}
            <strong>
              {{$currChamp.object_spec}}
            </strong>
          {{/if}}
          </td>
          {{else}}
          <td {{if $currChamp.keytable==$currChamp.BDD_name}}
                class="ok"
              {{/if}}>
            {{$currChamp.BDD_name}}
          </td>
          <td {{if $currChamp.error_BDD_type}}
                class="warning"
              {{elseif $currChamp.keytable==$currChamp.BDD_name}}
                class="ok"
              {{/if}}>
            {{$currChamp.BDD_type}}
            {{if $currChamp.error_BDD_type}}
              &mdash; <strong>{{$currChamp.error_BDD_type}}</strong>
            {{/if}}
          </td>
          <td {{if $currChamp.error_BDD_null}}
                class="warning"
              {{elseif $currChamp.keytable==$currChamp.BDD_name}}
                class="ok"
              {{/if}}>
            {{if $currChamp.BDD_null!="NO"}}
            {{$currChamp.BDD_null}}
            {{/if}}
          </td>
          <td {{if $currChamp.keytable==$currChamp.BDD_name}}
                class="ok"
              {{/if}}>
            {{$currChamp.BDD_default}}
          </td>
          <td {{if $currChamp.keytable==$currChamp.BDD_name}}
                class="ok"
              {{/if}}>
            {{$currChamp.BDD_index}}
          </td>
          {{/if}}
        </tr>
        {{/foreach}}
        {{/if}}
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>