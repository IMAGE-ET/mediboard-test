<table class="form">
  <tr>
    <th colspan="2" class="title">
      Statistiques des posologies utilisées pour {{$produit->libelle}}
    </th>
  </tr>
  <tr>
    {{if $praticien->_id}}
      <th class="category">{{$praticien->_view}}</th>
    {{/if}}
    <th class="category">Global</th>
  </tr>
	{{assign var="types" value=$prescription->_specs.type}}
	{{foreach from=$types->_list item=_type}}
	<tr>
	  <th colspan="2" class="category">{{tr}}CPrescription.type.{{$_type}}{{/tr}}</th>
	</tr>
	<tr>
	{{foreach from=$stats item=stats_by_owner key=owner}}
	  <td style="padding: 0px;">
	    <table class="tbl">
		      {{foreach from=$stats_by_owner.$_type item=_stats name="stat_prat"}}
  	      <tr>
  	        <td style="width: 40px;">{{$_stats.pourcentage}}%</td>
  	        <td style="width: 30px;">({{$_stats.occ}})</td>
  	        <td class="text">
  	          {{$_stats.view}}
  	        </td>
		      </tr>
		 	    {{foreachelse}}
		 	    <tr>
		 	      <td>
					    Aucune posologie
					  </td>
					</tr>
					{{/foreach}}
		  </table>
	  </td>
		{{/foreach}}
	</tr>
	{{/foreach}}
</table>