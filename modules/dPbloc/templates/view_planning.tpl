<!-- $Id$ -->

<script type="text/javascript">

function printAdmission(id) {
  var url = new Url;
  url.setModuleAction("dPadmissions", "print_admission");
  url.addParam("id", id);
  url.popup(700, 550, "Patient");
}

</script>


<table class="main">
  <tr>
    <th>
      <a href="#" onclick="window.print()">
        Planning du {{$filter->_date_min|date_format:"%d/%m/%Y"}}
        {{if $filter->_date_min != $filter->_date_max}}
        au {{$filter->_date_max|date_format:"%d/%m/%Y"}}
        {{/if}}
      </a>
    </th>
  </tr>
  {{foreach from=$plagesop item=curr_plageop}}
  <tr>
    <td class="text">
	  <strong>Dr {{$curr_plageop->_ref_chir->_view}}</strong> -
	  <strong>{{$curr_plageop->_ref_salle->nom}}</strong>
	  de {{$curr_plageop->debut|date_format:"%Hh%M"}} à {{$curr_plageop->fin|date_format:"%Hh%M"}}
    le {{$curr_plageop->date|date_format:"%d/%m/%Y"}}
    
    {{if $curr_plageop->anesth_id}}
	    - Anesthesiste : <strong>Dr {{$curr_plageop->_ref_anesth->_view}}</strong>
	  {{/if}}
	  
	  {{assign var="plageOp_id" value=$curr_plageop->_id}}
	  {{if $affectations_plage.$plageOp_id.op}}
	  -Aide-opératoires:
	    {{foreach from=$affectations_plage.$plageOp_id.op item=_personnel}}
      {{$_personnel->_ref_personnel->_ref_user->_view}};
      {{/foreach}}
	  {{/if}}
	  
	  {{if $affectations_plage.$plageOp_id.op_panseuse}}
	  -Panseuses:
	    {{foreach from=$affectations_plage.$plageOp_id.op_panseuse item=_personnel}}
      {{$_personnel->_ref_personnel->_ref_user->_view}};
      {{/foreach}}	  
	  {{/if}}
		</td>
  </tr>
  <tr>
    <td>
	</td>
  </tr>
      
  <tr>
    <td>
	  <table class="tbl">
	    <tr>
	    	{{include file=inc_planning/interv_title.tpl}}
	    	{{include file=inc_planning/sejour_title.tpl}}
	    	{{include file=inc_planning/patient_title.tpl}}
		</tr>
		<tr>
			{{include file=inc_planning/interv_header.tpl}}
			{{include file=inc_planning/sejour_header.tpl}}
			{{include file=inc_planning/patient_header.tpl}}
		</tr>
		{{foreach from=$curr_plageop->_ref_operations item=curr_op}}
		<tr>
		  	{{include file=inc_planning/interv_content.tpl}}
		  	
		  	{{assign var=sejour value=$curr_op->_ref_sejour}}
      		{{include file=inc_planning/sejour_content.tpl}}
      		
      		{{assign var=patient value=$sejour->_ref_patient}}
			{{include file=inc_planning/patient_content.tpl}}
		</tr>
		{{/foreach}}
	  </table>
	</td>
  </tr>
  {{/foreach}}
</table>