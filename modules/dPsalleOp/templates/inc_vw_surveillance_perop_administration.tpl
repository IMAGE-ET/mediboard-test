{{mb_script module=prescription script=prescription}}

<script>
  window.DMI_operation_id = '{{$operation->_id}}';
  Main.add(Prescription.updatePerop.curry({{$sejour->_id}}));
</script>

<div id="perop"></div>