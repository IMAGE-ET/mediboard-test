<script type="text/javascript">
  Main.add(function(){
    var form = getForm("edit-token");
    var element = form.elements._user_id_autocomplete_view;
    var url = new Url("system", "ajax_seek_autocomplete");
    
    url.addParam("object_class", "CMediusers");
    url.addParam("input_field", element.name);
    url.autoComplete(element, null, {
      minChars: 3,
      method: "get",
      select: "view",
      dropdown: true,
      afterUpdateElement: function(field,selected){
        var id = selected.getAttribute("id").split("-")[2];
        $V(form.user_id, id);
        if ($V(element) == "") {
          $V(element, selected.down('.view').innerHTML);
        }
      }
    });
  });
</script>
      
<form name="edit-token" action="" method="post" onsubmit="return onSubmitFormAjax(this)">

<input type="hidden" name="m" value="admin" />
{{mb_class object=$token}}
{{mb_key object=$token}}
<input type="hidden" name="del" value="0" />
<input type="hidden" name="callback" value="ViewAccessToken.refreshAll" />

<table class="form">
  {{mb_include module=system template=inc_form_table_header object=$token}}
  
  <tr>
    <th>{{mb_label object=$token field="user_id"}}</th>
    <td>
      <input type="text" name="_user_id_autocomplete_view" style="width: 16em;" class="autocomplete" 
      {{if $token->user_id}} value="{{$token->_ref_user->_view}}" {{/if}}
      onchange='if(!this.value){this.form.user_id.value=""}' />
      <input type="hidden" name="user_id" value="{{$token->user_id}}" />
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$token field="datetime_start"}}</th>
    <td>{{mb_field object=$token field="datetime_start" register=true form="edit-token"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$token field="ttl_hours"}}</th>
    <td>{{mb_field object=$token field="ttl_hours" increment=true form="edit-token"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$token field="params"}}</th>
    <td>{{mb_field object=$token field="params" size=50}}</td>
  </tr>
  
  {{if $token->_id}}
    <tr>
      <th>{{mb_label object=$token field="first_use"}}</th>
      <td>{{mb_value object=$token field="first_use"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$token field="hash"}}</th>
      <td>{{mb_value object=$token field="hash"}}</td>
    </tr>
  {{/if}}
  
  <tr>
    <td class="button" colspan="2">
      {{if $token->_id}}
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
        
        <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$token->_view|smarty:nodefaults|JSAttribute}}'})">
          {{tr}}Delete{{/tr}}
        </button>
      {{else}}
        <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
      {{/if}}
    </td>
  </tr>
</table>
