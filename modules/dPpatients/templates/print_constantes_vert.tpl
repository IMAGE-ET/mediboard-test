{{unique_id var=uniq_ditto}}
{{assign var="one_date" value="1"}}
{{assign var=cste_grid value=$constantes_medicales_grid}}

<table class="tbl" style="width: 40%;">
  <tr>
  	<th></th>
    {{foreach from=$cste_grid.grid item=_constante key=_datetime}}
      <th style="text-align: center;">
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
        <td style="text-align: center;">{{$_constante.values.$_cste_name}}</td>
			{{/foreach}}
     </tr>
	{{/foreach}}
</table>
