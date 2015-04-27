{{mb_default var=offline value=0}}
{{mb_default var=empty_lines value=0}}

{{unique_id var=uniq_ditto}}
{{assign var=cste_grid value=$constantes_medicales_grid}}

<table class="tbl constantes print_constante" style="width: 1%; font-size: inherit;">
  {{if $offline && isset($sejour|smarty:nodefaults)}}
    <thead>
      <tr>
        <th class="title"></th>
        <th class="title" colspan="{{$cste_grid.names|@count}}">
          {{$sejour->_view}}
          {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$sejour}}
        </th>
      </tr>
      <tr>
        <th class="narrow" style="page-break-inside: avoid;"></th>
        {{assign var=list_constantes value="CConstantesMedicales"|static:list_constantes}}
        {{foreach from=$cste_grid.names item=_cste_name}}
          <th class="narrow" style="vertical-align: bottom; font-weight: normal;">
            {{vertical}}
            {{if array_key_exists("cumul_for", $list_constantes.$_cste_name)}}
              Cumul {{tr}}CConstantesMedicales-{{$list_constantes.$_cste_name.cumul_for}}-court{{/tr}}
            {{else}}
              {{tr}}CConstantesMedicales-{{$_cste_name}}-court{{/tr}}
            {{/if}}

            {{if $list_constantes.$_cste_name.unit}} ({{$list_constantes.$_cste_name.unit}}){{/if}}
            {{/vertical}}
          </th>
        {{/foreach}}
      </tr>
    </thead>
  {{/if}}

  {{if $cste_grid.grid|@count}}
    {{assign var=list_constantes value="CConstantesMedicales"|static:list_constantes}}
    {{if !$offline}}
      <tr>
        <th class="narrow" style="page-break-inside: avoid;"></th>
        {{foreach from=$cste_grid.names item=_cste_name}}
          <th class="narrow" style="vertical-align: bottom; font-weight: normal;">
            {{vertical}}
              {{if array_key_exists("cumul_for", $list_constantes.$_cste_name)}}
                Cumul {{tr}}CConstantesMedicales-{{$list_constantes.$_cste_name.cumul_for}}-court{{/tr}}
              {{else}}
                {{tr}}CConstantesMedicales-{{$_cste_name}}-court{{/tr}}
              {{/if}}

              {{if $list_constantes.$_cste_name.unit}} ({{$list_constantes.$_cste_name.unit}}){{/if}}
            {{/vertical}}
          </th>
        {{/foreach}}
      </tr>
    {{/if}}
    
    {{foreach from=$cste_grid.grid item=_constante key=_datetime}}
      {{assign var=_datetime value=$_datetime|substr:0:18}}
      
      <tr class="comment-line">
        <th style="text-align: left;">
          {{$_datetime|date_format:$conf.datetime}}
          {{if $_constante.comment}}
            {{if $app->user_prefs.constantes_show_comments_tooltip}}
              <img src="style/mediboard/images/buttons/comment.png" title="{{$_constante.comment}}">
            {{else}}
              <div style="min-width: 120px; font-weight: normal; background: #eee; background: rgba(255,255,255,0.6); white-space: normal; text-align: left; padding: 2px; border: 1px solid #ddd;">
                {{$_constante.comment}}
              </div>
            {{/if}}
          {{/if}}
        </th>
        {{foreach from=$cste_grid.names item=_cste_name}}
          {{assign var=_value value=null}}
          
          {{if array_key_exists($_cste_name,$_constante.values)}}
            {{assign var=_value value=$_constante.values.$_cste_name}}
          {{/if}}
            
          {{if is_array($_value)}}
            {{if $_value.value === null}}
              <td style="{{if $_value.color}}background-color: {{$_value.color}};{{/if}}" {{if $_value.span > 0}} rowspan="{{$_value.span}}" {{/if}}></td>
            {{else}}
              <td style="text-align: center; font-size: 0.9em; border-left: 2px solid {{if $_value.pair == "odd"}} #36c {{else}} #3c9 {{/if}}; border-top: 1px solid #999; {{if $_value.color}}background-color: {{$_value.color}};{{/if}}" 
                  {{if $_value.span > 0}} rowspan="{{$_value.span}}" {{/if}}>
                <strong>{{$_value.value}}</strong> <br />
                <small>{{$_value.day}}</small>
              </td>
            {{/if}}
          {{elseif $_value != "__empty__"}}
            <td style="text-align: center; font-size: 0.9em;" >
              {{if $_value !== ""}}{{$_value}}{{else}}&nbsp;{{/if}}
            </td>
          {{else}}
            <!--<td></td>-->
          {{/if}}
        {{/foreach}}
      </tr>
    {{/foreach}}
    {{if $empty_lines}}
      {{foreach from=1|range:$empty_lines item=i}}
        <tr>
          <td style="height: 30px;"></td>
          {{foreach from=$cste_grid.names item=_cste_name}}
            <td></td>
          {{/foreach}}
        </tr>
      {{/foreach}}
    {{/if}}
  {{else}}
    <tr>
      <td></td>
      <td class="empty">{{tr}}CConstantesMedicales.none{{/tr}}</td>
    </tr>
  {{/if}}
</table>
