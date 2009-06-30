<form name="timing{{$selOp->operation_id}}" action="?m={{$m}}" method="post">

<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="dosql" value="do_planning_aed" />
<input type="hidden" name="operation_id" value="{{$selOp->operation_id}}" />
<input type="hidden" name="del" value="0" />

<table class="form">
  <tr>
    <th class="category" colspan="3">Horodatage</th>
  </tr>

	{{assign var=submit value=submitTiming}}
  {{assign var=opid value=$selOp->operation_id}}
  {{assign var=form value=timing$opid}}
  <tr>
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
