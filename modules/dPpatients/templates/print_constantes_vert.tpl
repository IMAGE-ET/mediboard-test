{{unique_id var=uniq_ditto}}
{{assign var="one_date" value="1"}}
{{assign var=cste_grid value=$constantes_medicales_grid}}

<table class="tbl" style="width: 40%;">
  <tr>
  	<th></th>
    {{foreach from=$cste_grid.grid item=_constante key=_datetime}}
      <th style="text-align: center; vertical-align: top;" class="text">
        {{mb_ditto name="datetime$uniq_ditto" value=$_datetime|date_format:$conf.datetime}}
				
				{{if $_constante.comment}}
				  <hr />
					{{$_constante.comment}}
				{{/if}}
      </th>
		{{/foreach}}
  </tr>
	
	{{foreach from=$cste_grid.names item=_cste_name}}
    <tr>
    	<th style="text-align: right;">
			  {{tr}}CConstantesMedicales-{{$_cste_name}}{{/tr}}
			</th>
			
      {{foreach from=$cste_grid.grid item=_constante key=_datetime}}
        {{assign var=_value value=$_constante.values.$_cste_name}}
        
        {{if is_array($_value)}}
          <td style="text-align: center; border-top: 2px solid {{if $_value.pair == "odd"}} #36c {{else}} #3c9 {{/if}}; border-left: 1px solid #999;" 
              {{if $_value.span > 0}} colspan="{{$_value.span}}" {{/if}}>
            <strong>{{$_value.value}}</strong> <br />
            {{$_value.day}}
          </td>
        {{elseif $_value != "__empty__"}}
          <td style="text-align: center;">
            {{$_value}}
          </td>
        {{/if}}
			{{/foreach}}
     </tr>
	{{/foreach}}
</table>
