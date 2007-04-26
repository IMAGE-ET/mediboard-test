<script type="text/javascript">
  Prescription.Examen.init({{$prescription_labo_examen_id}})
</script>
  

<form name="dropPrescriptionItem" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="dPlabo" />
  <input type="hidden" name="dosql" value="do_prescription_examen_aed" />
  <input type="hidden" name="_pack_examens_labo_id" value="" />
  <input type="hidden" name="prescription_labo_examen_id" value="" />
  <input type="hidden" name="examen_labo_id" value="" />
  <input type="hidden" name="prescription_labo_id" value="" />
  <input type="hidden" name="del" value="0" />
</form>

{{foreach from=$patient->_ref_prescriptions item="curr_prescription"}}
<div class="tree-header {{if $curr_prescription->_id == $prescription->_id}}selected{{/if}}" id="drop-prescription-{{$curr_prescription->_id}}">
  <script type="text/javascript">
  Droppables.add('drop-prescription-{{$curr_prescription->_id}}', {
    onDrop: function(element) {
      Prescription.Examen.drop(element.id, {{$curr_prescription->_id}})
    }, 
    hoverclass:'selected'
  } );
  </script>
  <div style="float:right;">
    {{$curr_prescription->_ref_prescription_labo_examens|@count}} Examens
  </div>
  <a href="#nothing" onclick="Prescription.select({{$curr_prescription->_id}})">
    {{$curr_prescription->_view}}
  </a>
  <br />
  <form name="delPrescription-{{$curr_prescription->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
    <input type="hidden" name="m" value="dPlabo" />
    <input type="hidden" name="dosql" value="do_prescription_aed" />
    <input type="hidden" name="prescription_labo_id" value="{{$curr_prescription->_id}}" />
    <input type="hidden" name="del" value="1" />
    <button type="button" class="trash notext" onclick="Prescription.del(this.form)" >{{tr}}Delete{{/tr}}</button>
    <button type="button" class="edit notext" onclick="Prescription.edit({{$curr_prescription->_id}});">edit</button>
  </form>
</div>
{{/foreach}}