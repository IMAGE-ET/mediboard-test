{{mb_default var=offline value=0}}
{{unique_id var=uniq_ditto}}
{{assign var=cste_grid value=$constantes_medicales_grid}}

<table class="tbl constantes" style="width: 100%;">
  {{if $offline && isset($sejour|smarty:nodefaults)}}
    <thead>
      <tr>
        <th class="title"></th>
        <th class="title" colspan="{{$cste_grid.names|@count}}">
          {{$sejour->_view}}
          {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$sejour->_num_dossier}}
        </th>
      </tr>
    </thead>
  {{/if}}

  {{if $cste_grid.grid|@count}}
	  <tr>
	    <th class="narrow"></th>
	    {{foreach from=$cste_grid.names item=_cste_name}}
	      <th class="narrow" style="vertical-align: bottom;">
	        {{vertical}}{{tr}}CConstantesMedicales-{{$_cste_name}}-court{{/tr}}{{/vertical}}
	      </th>
	    {{/foreach}}
	  </tr>
	  
	  {{foreach from=$cste_grid.grid item=_constante key=_datetime}}
	    <tr class="comment-line">
	      <th {{if $_constante.comment}} rowspan="2" {{/if}}>
	        {{mb_ditto name="datetime$uniq_ditto" value=$_datetime|date_format:$conf.datetime}}
	      </th>
	      {{foreach from=$cste_grid.names item=_cste_name}}
	        <td style="text-align: center; font-size: 0.9em;">
					  {{$_constante.values.$_cste_name}}
				  </td>
	      {{/foreach}}
	    </tr>
	    {{if $_constante.comment}}
	      <tr>
	        <td colspan="{{$cste_grid.names|@count}}">
	          {{tr}}CConstantesMedicales-comment-court{{/tr}}:
	          {{$_constante.comment}}
	        </td>
	      </tr>
	    {{/if}}
	  {{/foreach}}
	
	{{else}}
	
    <tr>
      <td></td>
      <td class="empty">{{tr}}CConstantesMedicales.none{{/tr}}</td>
    </tr>
		
	{{/if}}
</table>
