<table class="main">
  <!-- Liste des classes -->
  <tr>
    <td style="text-align: center;">
      <form action="?" name="mntTable" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
  
        <label for="class" title="Veuillez Sélectionner une classe">Choix de la classe</label>
        <select class="notNull str" name="class">
          <option value=""{{if !$class}} selected="selected"{{/if}}>&mdash; Liste des erreurs</option>
          {{foreach from=$installed_classes item=_class}}
          <option value="{{$_class}}" {{if $class == $_class}} selected="selected"{{/if}}>{{$_class}} - {{tr}}{{$_class}}{{/tr}}</option>
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
    <td>
      <form action="?" name="csv-class-table" method="get" target="_blank">
        <input type="hidden" name="m" value="dPdeveloppement" />
        <input type="hidden" name="a" value="csv_class_tables" />
        <input type="hidden" name="suppressHeaders" value="1" />
        <button class="change">CSV classes / tables</button>
      </form>
    </td>
  </tr>
  
  <tr>
    <td colspan="2">
      <div class="big-info">Pour chaque spécification de propriété : 
        <ul>
          <li><strong>la première ligne</strong> correspond au mapping objet => relationnel théorique,</li> 
          <li><strong>la deuxième ligne </strong>correspond à ce qui est réellement présent dans la base de données.</li>
        </ul>
      </div>
    </td>
  </tr>
  
  <tr>
    <td colspan="2">
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
        
        {{foreach from=$list_classes key=_class item=_class_details}}
          {{if $list_errors.$_class || $list_classes|@count == 1}}
            {{if $_class_details.suggestion}}
            <tr>
              <th colspan="11" class="title">
                <button id="sugg-{{$_class}}-trigger" class="edit" style="float: left;">
                  {{tr}}Suggestion{{/tr}}
                </button>
                {{$_class}} ({{tr}}{{$_class}}{{/tr}})
              </th>
            </tr>
            <tr id="sugg-{{$_class}}">
              <td colspan="100">
                <script type="text/javascript">new PairEffect('sugg-{{$_class}}', {bStoreInCookie: false});</script>
                <pre>{{$_class_details.suggestion}}</pre>
              </td>
            </tr>
            {{/if}}
          {{foreach from=$_class_details.fields key=_field_name item=_field}}
            
            {{if $list_errors.$_class.$_field_name || $_class_details.key == $_field_name || $class == $_class}}
            <tr>
              <td {{if $_class_details.key == $_field_name}}class="ok"{{/if}}>{{$_field_name}}</td>
              
              {{if !$_field.object.spec}}
                <td class="warning text">Aucune spec</td>
              {{else}}
                <td class="text" title="{{$_field.object.spec}}">{{$_field.object.spec}}</td>
              {{/if}}
              
              <td class="text">
                {{if $_field.object.db_spec}}
                    {{$_field.object.db_spec.type}}
                    
                    {{if $_field.object.db_spec.params|@count > 0}}
                    (
                      {{foreach from=$_field.object.db_spec.params item=param name=params}}
                        {{$param}}{{if !$smarty.foreach.params.last}},{{/if}} 
                      {{/foreach}}
                    )
                    {{/if}}
                    
                    {{if $_field.object.db_spec.unsigned}}UNSIGNED{{/if}}
                    {{if $_field.object.db_spec.zerofill}}ZEROFILL{{/if}}
                  
                    {{if !$_field.object.db_spec.null}}NOT NULL{{/if}}
                    {{if $_field.object.db_spec.default !== null}}DEFAULT {{$_field.object.db_spec.default}}{{/if}}
                {{else}}
                  <div class="error">
                    Pas de spec pour cette colonne
                  </div>
                {{/if}}
                &nbsp;
                <hr style="border: 0; border-top: 1px solid #CCC; margin: 1px;" />
                
                {{if !$_class_details.no_table}}
                  {{if $_field.db}}
                    <span {{if $_field.db.type != $_field.object.db_spec.type}}class="warning"{{/if}}>
                      {{$_field.db.type}}
                    </span>
                    
                    <span {{if $_field.db.params != $_field.object.db_spec.params}}class="warning"{{/if}}>
                      {{if $_field.db.params|@count > 0}}
                      (
                        {{foreach from=$_field.db.params item=param name=params}}
                          {{$param}}{{if !$smarty.foreach.params.last}},{{/if}}
                        {{/foreach}}
                      )
                      {{/if}}
                    </span>
                  
                    <span {{if $_field.db.unsigned != $_field.object.db_spec.unsigned}}class="warning"{{/if}}>
                      {{if $_field.db.unsigned}}UNSIGNED{{/if}}
                    </span>
                    
                    <span {{if $_field.db.zerofill != $_field.object.db_spec.zerofill}}class="warning"{{/if}}>
                      {{if $_field.db.zerofill}}ZEROFILL{{/if}}
                    </span>
                    
                    <span {{if $_field.db.null != $_field.object.db_spec.null}}class="warning"{{/if}}>
                      {{if !$_field.db.null}}NOT NULL{{/if}}
                    </span>

                    <span {{if $_field.db.default != $_field.object.db_spec.default}}class="warning"{{/if}}>
                      {{if $_field.db.default !== null && $_field.db.default !== ''}}DEFAULT {{$_field.db.default}} {{/if}}
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
                {{$_field.object.db_spec.default}}&nbsp;<hr style="border: 0; border-top: 1px solid #CCC; margin: 1px;" />
                <span {{if $_field.db.default != $_field.object.db_spec.default}}class="warning"{{/if}}>
                  {{$_field.db.default}}&nbsp;
                </span>
              </td>
              
              <td>
                {{if $_field.object.db_spec.index}}Oui{{else}}Non{{/if}}&nbsp;<hr style="border: 0; border-top: 1px solid #CCC; margin: 1px;" />
                <span 
                  {{if $_field.object.db_spec.index && !$_field.db.index}}
                    class="error"
                  {{elseif !$_field.object.db_spec.index && $_field.db.index}}
                    class="warning"
                  {{/if}}>
                  {{if $_field.db.index}}Oui{{else}}Non{{/if}}&nbsp;
                </span>
              </td>
              
              <td>
                {{$_field.object.db_spec.extra}}&nbsp;<hr style="border: 0; border-top: 1px solid #CCC; margin: 1px;" />
                <span {{if $_field.db.extra != $_field.object.db_spec.extra}}class="warning"{{/if}}>
                  {{$_field.db.extra}}&nbsp;
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