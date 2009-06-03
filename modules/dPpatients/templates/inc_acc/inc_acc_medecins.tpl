<script type="text/javascript">

calculFinAmo = function(){  
  var oForm = document.editFrm;
  var sDate = oForm.fin_amo.value;  
      
  if(($V(oForm.cmu) == 1 && sDate == "")){
    date = new Date;
    date.addDays(365);
    oForm.fin_amo.value = date.toDATE();
    oForm.fin_amo_da.value = date.toLocaleDate();
  }  
}

</script>

{{mb_include_script module="dPpatients" script="widget_correspondants"}}

<script type="text/javascript">
  var corresp = new Correspondants({{$patient->_id}});
</script>