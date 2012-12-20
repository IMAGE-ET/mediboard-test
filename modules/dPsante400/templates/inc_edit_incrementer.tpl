<script type="text/javascript">

getVars = function(input){
  var url = new Url("dPsante400", "ajax_get_vars");
  url.addParam("object_class", $V(input));
  url.requestUpdate("object_class_vars");
}

Main.add(function(){
  var oForm = getForm("incrementer{{$incrementer->_guid}}");
  
  oForm.range_min.addSpinner({min: 0});
  oForm.range_max.addSpinner({min: 0});
  
  getVars(oForm._object_class);
});
    
</script>

<form name="incrementer{{$incrementer->_guid}}" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, Control.Modal.close);">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="dosql" value="do_incrementer_aed" />
  <input type="hidden" name="incrementer_id" value="{{$incrementer->_id}}" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="last_update" value="now" />
  <input type="hidden" name="callback" value="Domain.bindIncrementerDomain" />

  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$incrementer}}
    
    <tr>
      <th>{{mb_label object=$incrementer field="_object_class"}}</th>
      <td>{{mb_field object=$incrementer field="_object_class" readonly=true}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$incrementer field="pattern"}}</th>
      <td>{{mb_field object=$incrementer field="pattern"}}</td>
    </tr>
    
    <tr>
      <th></th>
      <td id="object_class_vars"></td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$incrementer field="range_min"}}</th>
      <td>{{mb_field object=$incrementer field="range_min"}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$incrementer field="range_max"}}</th>
      <td>{{mb_field object=$incrementer field="range_max"}}</td>
    </tr>

    <tr>
      <td class="button" colspan="2">
        {{if $incrementer->_id}}
          <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
        {{else}}
           <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr> 
  </table>
</form>