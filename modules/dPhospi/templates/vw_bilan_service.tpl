<table class="form">
  <tr>
    <td>
     <form name="filter_prescription" action="?" method="get">
      <input type="hidden" name="m" value="dPhospi" />
      <input type="hidden" name="a" value="vw_bilan_service" />
      <input type="hidden" name="dialog" value="1" />
      De {{mb_field object=$prescription field="_filter_time_min" form="filter_prescription"}}
      à {{mb_field object=$prescription field="_filter_time_max" form="filter_prescription"}}
      <button class="tick">Filtrer</button>
      </form>
    </td>
  </tr>
</table>

<table class="tbl">
  <tr>
    <th>Libelle</th>
    <th>Prises</th>
  </tr>
	{{foreach from=$prises key=patient_id item=lines_by_patient_class_hour}}
	  {{assign var=patient value=$patients.$patient_id}}
	  <tr>
		 <th colspan="2">{{$patient->_view}}</th>
		</tr>
	  {{foreach from=$lines_by_patient_class_hour key=hour item=lines_by_patient_class}}
		  {{foreach from=$lines_by_patient_class key=_class item=lines_by_patient}}	  
			  <tr>
	        <th colspan="2">{{$hour}}</th>
	      </tr>
			  {{foreach from=$lines_by_patient key=line_id item=prises_by_patient}}
			  {{assign var=produit value=$lines_produit.$_class.$line_id}}
			  <tr>
			    <td>{{$produit->_view}}</td>
				  <td>
				  {{foreach from=$prises_by_patient item=prise}}
				    {{$prise->_view}}
				  {{/foreach}}
				  </td>
			  </tr>
			  {{/foreach}}
			  </tr>
		  {{/foreach}}
	  {{/foreach}}
	{{/foreach}}
</table>