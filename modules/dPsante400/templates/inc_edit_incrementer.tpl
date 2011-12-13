<script type="text/javascript">

getVars = function(input){
  var url = new Url("dPsante400", "ajax_get_vars");
  url.addParam("object_class", $V(input));
  url.requestUpdate("object_class_vars");
}

{{if $incrementer->_id}}
  Main.add(function () {
	  getVars(getForm("incrementer{{$incrementer->_guid}}").object_class);
  });
{{/if}}

</script>


<form name="incrementer{{$incrementer->_guid}}" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this);">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="dosql" value="do_incrementer_aed" />
  <input type="hidden" name="incrementer_id" value="{{$incrementer->_id}}" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="last_update" value="now" />

  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$incrementer}}
    
    <tr>
      <th>{{mb_label object=$incrementer field="group_id"}}</th>
      <td>{{mb_field object=$incrementer field="group_id" form="incrementer`$incrementer->_guid`" autocomplete="true,1,50,true,true"}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$incrementer field="object_class"}}</th>
      <td>{{mb_field object=$incrementer field="object_class" onchange="getVars(this)" emptyLabel=" "}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$incrementer field="pattern"}}</th>
      <td>{{mb_field object=$incrementer field="pattern"}}</td>
    </tr>

    <tr>
      <td class="button" colspan="2">
        {{if $incrementer->_id}}
          <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
          <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$incrementer->_view|smarty:nodefaults|JSAttribute}}',ajax:true})">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
           <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr> 
  </table>
  
  <div id="object_class_vars">
      
  </div>
</form>