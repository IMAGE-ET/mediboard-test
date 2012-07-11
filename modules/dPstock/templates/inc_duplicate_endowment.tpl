<script type="text/javascript">
endowmentDuplicateCallback = function(id){
  getForm("filter-endowments").onsubmit();
  Control.Modal.close();
}
</script>

<form name="duplicate_endowment" action="" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_product_endowment_aed" />
  <input type="hidden" name="m" value="dPstock" />
  <input type="hidden" name="callback" value="endowmentDuplicateCallback" />
  {{mb_key object=$endowment}}
  
  <table class="form">
    <tr>
      <th class="title" colspan="2">Duplication de la dotation {{$endowment}}</th>
    </tr>
    
    <tr>
      <th>{{mb_label object=$endowment field="_duplicate_to_service_id"}}</th>
      <td>{{mb_field object=$endowment field="_duplicate_to_service_id" form="duplicate_endowment" autocomplete="true,1,50,false,true"}}</td>
    </tr>
    
    <tr>
      <td class="button" colspan="2">
        <button class="hslip" type="submit">{{tr}}Duplicate{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>