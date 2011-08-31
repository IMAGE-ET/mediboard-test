<form name="timing{{$selOp->operation_id}}" action="?m={{$m}}" method="post">

<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="dosql" value="do_planning_aed" />
<input type="hidden" name="operation_id" value="{{$selOp->operation_id}}" />
<input type="hidden" name="del" value="0" />

<table class="form" style="table-layout: fixed;">
  <tr>
    <th class="title" colspan="4">Horodatage</th>
		
  </tr>

	{{assign var=submit value=submitTiming}}
  {{assign var=opid value=$selOp->operation_id}}
  {{assign var=form value=timing$opid}}
  <tr>
  	{{if @$modules.brancardage->_can->read}}
	    <input type="hidden" name="patientpaspret" value="0" id="patientpaspret" />
	  	<td id="demandebrancard" rowspan="2">
		    <input type="hidden" name="opid" value="{{$opid}}" id="opid" />
		    <input type="hidden" name="sejour_id" value="{{$selOp->sejour_id}}" id="sejour_id" />
		    <input type="hidden" name="salle_id" value="{{$selOp->salle_id}}" id="salle_id" />
		    <button type="button" class="brancard" onclick="CreationBrancard.demandeBrancard('{{$selOp->sejour_id}}','{{$selOp->salle_id}}', '{{$opid}}');" >
		      Demande Brancardage
	      </button>
	    </td>
	    {{mb_script module=brancardage script=creation_brancardage ajax=true}}
    {{/if}}
    
    {{include file=inc_field_timing.tpl object=$selOp field=entree_salle}}
    {{include file=inc_field_timing.tpl object=$selOp field=pose_garrot }}
    {{include file=inc_field_timing.tpl object=$selOp field=debut_op    }}
  </tr>
  <tr>
    {{include file=inc_field_timing.tpl object=$selOp field=sortie_salle  }}
    {{include file=inc_field_timing.tpl object=$selOp field=retrait_garrot}}
    {{include file=inc_field_timing.tpl object=$selOp field=fin_op        }}
  </tr>
</table>

</form>
