<table class="main">
	
<tr>
  <td class="halfPane">
    <a href="#" onclick="showInfrastructure('uf_id', '0', 'infrastructure_uf')" class="button new">
      {{tr}}CUniteFonctionnelle-title-create{{/tr}}
    </a>
    
    <!-- Liste des services -->
    <table class="tbl">
	    <tr>
	      <th colspan="3" class="title">
	        {{tr}}CUniteFonctionnelle.all{{/tr}}
	      </th>
	    </tr>
	    <tr>
	      <th>{{mb_title class=CUniteFonctionnelle field=code}}</th>
	      <th>{{mb_title class=CUniteFonctionnelle field=libelle}}</th>
        <th>{{mb_title class=CUniteFonctionnelle field=description}}</th>
	    </tr>
	
			{{foreach from=$ufs item=_uf}}
	    <tr {{if $_uf->_id == $uf->_id}}class="selected"{{/if}}>
	      <td>
	      	<a href="#" onclick="showInfrastructure('uf_id', '{{$_uf->_id}}', 'infrastructure_uf')">
	          {{mb_value object=$_uf field=code}}
					</a>
				</td>
	      <td class="text">{{mb_value object=$_uf field=libelle}}</td>
	      <td class="text">{{mb_value object=$_uf field=description}}</td>
	    </tr>
	    {{/foreach}}
    </table>
  </td> 

  <td class="halfPane" id="infrastructure_uf">
  	{{mb_include module=dPhospi template=inc_vw_uf}}
  </td>
</tr>

</table>
