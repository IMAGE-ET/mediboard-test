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
  <option type="hidden" value="0" id="patientpaspret"></option>
  	<td id="demandebrancard" >
            <option type="hidden" value="{{$opid}}" id="opid"></option>
            <option type="hidden" value="{{$selOp->sejour_id}}" id="sejour_id"></option>
            <option type="hidden" value="{{$selOp->salle_id}}" id="salle_id"></option>
            <button type="button" class="submit" onclick="CreationBrancard.demandeBrancard('{{$selOp->sejour_id}}','{{$salle}}', '{{$opid}}');" >
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
  	<td></td>
    {{include file=inc_field_timing.tpl object=$selOp field=sortie_salle  }}
    {{include file=inc_field_timing.tpl object=$selOp field=retrait_garrot}}
    {{include file=inc_field_timing.tpl object=$selOp field=fin_op        }}
  </tr>
</table>

</form>
