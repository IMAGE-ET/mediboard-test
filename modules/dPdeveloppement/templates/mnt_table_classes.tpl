<table class="main">
  <!-- Liste des classes -->
  <tr>
    <td style="text-align: center;">
      <form action="?" name="mntTable" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
  
        <label for="class_name" title="Veuillez Sélectionner une classe">Choix de la classe</label>
        <select class="notNull str" name="class_name">
          <option value=""{{if !$class_name}} selected="selected"{{/if}}>&mdash; Liste des erreurs</option>
          {{foreach from=$list_class_names item=curr_class_name}}
          <option value="{{$curr_class_name}}"{{if $class_name==$curr_class_name}} selected="selected"{{/if}}>{{$curr_class_name}} - {{tr}}{{$curr_class_name}}{{/tr}}</option>
          {{/foreach}}
        </select>
        <br />
        
        {{foreach from=$types key=type item=value}}
          <input type="checkbox" name="types[]" value="{{$type}}" {{if $value}}checked="checked"{{/if}} />{{$type}}
        {{/foreach}}
        <br />
        
        <button name="submit" class="search">Filtrer</button>
      </form>
    </td>
  </tr>
	
	<tr>
	  <td>
      <div class="big-info">Pour chaque spécification de propriété : 
      	<ul>
      	  <li><strong>la première ligne</strong> correspond au mapping objet => relationnel théorique,</li> 
					<li><strong>la deuxième ligne </strong>correspond à ce qui est réellement présent dans la base de données.</li>
				</ul>
			</div>
	  </td>
	</tr>
  
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th rowspan="2">Champ</th>
          <th rowspan="2">Spec object</th>
          <th colspan="8">Base de données</th>
        </tr>
        <tr>
          <th>Type</th>
          <th>Default</th>
          <th>Index</th>
          <th>Extra</th>
        </tr>
        
        {{foreach from=$list_classes key=curr_class_name item=curr_class}}
          {{if $list_errors.$curr_class_name || $list_classes|@count == 1}}
            {{if $$curr_class.suggestion}}
	          <tr>
	            <th colspan="11" class="title">
	              <button id="sugg-{{$curr_class_name}}-trigger" class="edit" style="float: left;">
	                {{tr}}Suggestion{{/tr}}
	              </button>
	              {{$curr_class_name}} ({{tr}}{{$curr_class_name}}{{/tr}})
	            </th>
	          </tr>
	          <tr id="sugg-{{$curr_class_name}}">
	            <td colspan="100">
	              <script type="text/javascript">new PairEffect('sugg-{{$curr_class_name}}', {bStoreInCookie: false});</script>
	              <pre>{{$curr_class.suggestion}}</pre>
	            </td>
	          </tr>
	          {{/if}}
          {{foreach from=$curr_class.fields key=curr_field_name item=curr_field}}
            
            {{if $list_errors.$curr_class_name.$curr_field_name || $curr_class.key == $curr_field_name || $class_name == $curr_class_name}}
            <tr>
              <td {{if $curr_class.key == $curr_field_name}}class="ok"{{/if}}>{{$curr_field_name}}</td>
              
              {{if !$curr_field.object.spec}}
                <td class="warning text">Aucune spec<br />&nbsp;</td>
              {{else}}
                <td class="text" title="{{$curr_field.object.spec}}">{{$curr_field.object.spec|replace:'|':' | '}}<br />&nbsp;</td>
              {{/if}}
              
              <td class="text">
                {{if $curr_field.object.db_spec}}
                    {{$curr_field.object.db_spec.type}}
                    
                    {{if $curr_field.object.db_spec.params|@count > 0}}
                    (
                      {{foreach from=$curr_field.object.db_spec.params item=param name=params}}
                        {{$param}}{{if !$smarty.foreach.params.last}},{{/if}} 
                      {{/foreach}}
                    )
                    {{/if}}
                    
                    {{if $curr_field.object.db_spec.unsigned}}UNSIGNED{{/if}}
                    {{if $curr_field.object.db_spec.zerofill}}ZEROFILL{{/if}}
                  
                    {{if !$curr_field.object.db_spec.null}}NOT NULL{{/if}}
                    {{if $curr_field.object.db_spec.default !== null}}DEFAULT {{$curr_field.object.db_spec.default}}{{/if}}
                {{else}}
                  <div class="error">
                    Pas de spec pour cette colonne
                  </div>
                {{/if}}
                &nbsp;
                <hr style="border: 0; border-top: 1px solid #CCC; margin: 1px;" />
                
                {{if !$curr_class.no_table}}
                  {{if $curr_field.db}}
                    <span {{if $curr_field.db.type != $curr_field.object.db_spec.type}}class="warning"{{/if}}>
                      {{$curr_field.db.type}}
                    </span>
                    
                    <span {{if $curr_field.db.params != $curr_field.object.db_spec.params}}class="warning"{{/if}}>
                      {{if $curr_field.db.params|@count > 0}}
                      (
                        {{foreach from=$curr_field.db.params item=param name=params}}
                          {{$param}}{{if !$smarty.foreach.params.last}},{{/if}}
                        {{/foreach}}
                      )
                      {{/if}}
                    </span>
                  
                    <span {{if $curr_field.db.unsigned != $curr_field.object.db_spec.unsigned}}class="warning"{{/if}}>
                      {{if $curr_field.db.unsigned}}UNSIGNED{{else}}&nbsp;{{/if}}
                    </span>
                    
                    <span {{if $curr_field.db.zerofill != $curr_field.object.db_spec.zerofill}}class="warning"{{/if}}>
                      {{if $curr_field.db.zerofill}}ZEROFILL{{else}}&nbsp;{{/if}}
                    </span>
                    
                    <span {{if $curr_field.db.null != $curr_field.object.db_spec.null}}class="warning"{{/if}}>
                      {{if !$curr_field.db.null}}NOT NULL{{else}}&nbsp;{{/if}}
                    </span>
  
                  {{else}}
                    <div class="error">
                      Pas de colonne pour cette spec
                    </div>
                  {{/if}}
                {{else}}
                  <div class="error">
                    Pas de table existante pour cette classe
                  </div>
                {{/if}}
              </td>
              
              <td>
                {{$curr_field.object.db_spec.default}}&nbsp;<hr style="border: 0; border-top: 1px solid #CCC; margin: 1px;" />
                <span {{if $curr_field.db.default != $curr_field.object.db_spec.default}}class="warning"{{/if}}>
                  {{$curr_field.db.default}}&nbsp;
                </span>
              </td>
              
              <td>
                {{if $curr_field.object.db_spec.index}}Oui{{else}}Non{{/if}}&nbsp;<hr style="border: 0; border-top: 1px solid #CCC; margin: 1px;" />
                <span 
                  {{if $curr_field.object.db_spec.index && !$curr_field.db.index}}
                    class="error"
                  {{elseif !$curr_field.object.db_spec.index && $curr_field.db.index}}
                    class="warning"
                  {{/if}}>
                  {{if $curr_field.db.index}}Oui{{else}}Non{{/if}}&nbsp;
                </span>
              </td>
              
              <td>
                {{$curr_field.object.db_spec.extra}}&nbsp;<hr style="border: 0; border-top: 1px solid #CCC; margin: 1px;" />
                <span {{if $curr_field.db.extra != $curr_field.object.db_spec.extra}}class="warning"{{/if}}>
                  {{$curr_field.db.extra}}&nbsp;
                </span>
              </td>
            </tr>
            {{/if}}
          {{/foreach}}
          {{/if}}
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>