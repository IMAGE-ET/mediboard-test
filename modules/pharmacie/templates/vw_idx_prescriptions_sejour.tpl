{{mb_include_script module="dPmedicament" script="medicament_selector"}}
{{mb_include_script module="dPmedicament" script="equivalent_selector"}}
{{mb_include_script module="dPprescription" script="element_selector"}}
{{mb_include_script module="dPprescription" script="prescription"}}

<table class="main">
  <tr>
    <td style="width: 150px;">
      <table class="tbl">  
        <tr>
          <th>Praticien</th>
          <th>Patient</th>
        </tr>
				{{foreach from=$prescriptions item=prescription}}
				<tr>
				  <td><a href="#{{$prescription->_id}}" onclick="Prescription.reload('{{$prescription->_id}}')">{{$prescription->_ref_praticien->_view}}</a></td>
				  <td>{{$prescription->_ref_object->_view}}</td>
				</tr>
				{{/foreach}}
      </table>
    </td>
    <td>
      <div id="produits_elements">
      {{*include file="../../dPprescription/templates/inc_vw_prescription.tpl" mode_protocole=0 prescription=$prescription*}}
      </div>
    </td>
  </tr>  
</table>