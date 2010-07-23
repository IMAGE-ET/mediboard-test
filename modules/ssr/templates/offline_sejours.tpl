{{mb_include_script module=ssr script=sejours_ssr}}
{{mb_include_script module="ssr" script="planning"}}
{{mb_include_script module="ssr" script="planification"}}

<table class="main tbl">
	{{mb_include template=inc_sejours_ssr sejours=$sejours offline=1}}
	
	{{foreach from=$sejours item=_sejour}}
	  {{assign var=sejour_id value=$_sejour->_id}}
	<div id="modal-view-{{$sejour_id}}" style="display: none; width: 700px;">
		<script type="text/javascript">
			Main.add(function(){
			  Control.Tabs.create("tabs-sejour-{{$sejour_id}}");
			});
		</script>
		
		<h1>{{$_sejour->_ref_patient->_view}}</h1>
		
		<ul id="tabs-sejour-{{$sejour_id}}" class="control_tabs">
			<li><a href="#prescription-{{$sejour_id}}">Prescription & Bilan SSR</a></li>
			<li onmouseup="$('{{$_sejour->_guid}}').down('.week-container').setStyle({height: '600px' });
			                (function(){ window['planning-{{$_sejour->_guid}}'].updateEventsDimensions(); }).defer();"><a href="#planning-{{$sejour_id}}">Planning</a></li>
			<li style="float: right"><button class="cancel" onclick="modalwindow.close();">Fermer</button></li>
		</ul>
		<hr class="control_tabs" />
		
		<div id="prescription-{{$sejour_id}}" style="display: none;">
			<table class="tbl">
				<tr>
					<th colspan="2" class="title">
						Prescription
					</th>
			  </tr>
				<tr>
					<th>
						 {{mb_label class="CElementPrescription" field="category_prescription_id"}}
					</th>
					<th>
						{{mb_label class="CPrescriptionLineElement"  field="element_prescription_id"}}
					</th>
				</tr>	
				{{foreach from=$_sejour->_ref_prescription_sejour->_ref_prescription_lines_element item=_line}}
					<tr>
						<td>
						  {{mb_ditto name="category" value=$_line->_ref_element_prescription->_ref_category_prescription->_view}}
						</td>	
						<td style="text-align: left;">
						 {{mb_include template=inc_vw_line offline=1}}
						</td>
					</tr>
				{{/foreach}}
				<tr>
					<th colspan="2" class="title">
						Bilan SSR
					</th>
				</tr>		
        <tr>
          <th colspan="2">
          	{{mb_label object=$_sejour->_ref_bilan_ssr field="entree"}}
					</th>
        </tr> 
				<tr>
					<td colspan="2" style="text-align: left;">
						{{mb_value object=$_sejour->_ref_bilan_ssr field="entree"}}
			 	 </td>
				</tr>
        <tr>
          <th colspan="2">
          	{{mb_label object=$_sejour->_ref_bilan_ssr field="sortie"}}
					</th>
        </tr> 
				<tr>
					<td colspan="2" style="text-align: left;">
						{{mb_value object=$_sejour->_ref_bilan_ssr field="sortie"}}
					</td>
				</tr>
	    </table>
		</div>
    <div id="planning-{{$sejour_id}}" style="display: none;">
      {{$plannings.$sejour_id|smarty:nodefaults}}
    </div>
	</div>
	{{/foreach}}
</table>