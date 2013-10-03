<script>
  calculFinAmo = function(){
    var oForm = getForm("editFrm");
    var sDate = oForm.fin_amo.value;

    if (($V(oForm.cmu) == 1 && sDate == "")) {
      date = new Date;
      date.addDays(365);
      oForm.fin_amo.value = date.toDATE();
      oForm.fin_amo_da.value = date.toLocaleDate();
    }
  }
</script>

{{mb_script module="patients" script="widget_correspondants" ajax=$ajax}}

<script>
  var corresp = new Correspondants('{{$patient->_id}}', {container: $('medecins')});
</script>