<table class="tbl" id="">
	<tr>
	  <th class="title">
	    <button class="change notext" style="float: left" onclick="updatePlanSoinsPatients();">{{tr}}Refresh{{/tr}}</button>
	    Patients
	  </th>
		<th class="title">
		  Ligne - Prises
		</th>	
	  <th class="title">08</th>
	  <th class="title">09</th>
	  <th class="title">10</th>
	  <th class="title">11</th>
	  <th class="title">12</th>
	  <th class="title">13</th>
	  <th class="title">14</th>
	  <th class="title">15</th>
	  <th class="title">16</th>
	  <th class="title">17</th>
	  <th class="title">18</th>
	  <th class="title">19</th>
	  <th class="title">20</th>   
	</tr>
	{{foreach from=$lines_by_patient key=patient_id item=_lines}}
	  <tr>
	  	<th rowspan="{{$_lines|@count}}" class="narrow">
	  		{{assign var=patient value=$patients.$patient_id}}
        {{$patient->_view}}
	  	</th>
		
	  {{foreach from=$_lines item=_line name=elts}}
		  {{if !$smarty.foreach.elts.first}}
		  <tr>
		  {{/if}}
		   <td class="text" style="width: 20%;">
		  		{{$_line->_view}}
					
					{{if $_line->_ref_prises|@count}}
					  <br />
	          <span style="opacity: 0.7">
			  		{{foreach from=$_line->_ref_prises item=_prise name=prises}}
						{{$_prise->_view}}{{if !$smarty.foreach.prises.last}}, {{/if}}
					  {{/foreach}}
						</span>
					{{/if}}
		  	</td>
	    </tr>   
		{{/foreach}}
	{{/foreach}}
</table>