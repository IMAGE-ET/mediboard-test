{{if $check_list->isReadonly()}}

  <table class="main tbl">
    {{assign var=category_id value=0}}
    {{foreach from=$check_list->_ref_item_types item=curr_type}}
      {{assign var=curr_cat value=$curr_type->category_id}}
      {{if array_key_exists($curr_cat, $check_item_categories)}}
      {{if $curr_type->category_id != $category_id}}
        <tr>
          <th colspan="3" class="text category" style="text-align: left;">
            <strong>{{$check_item_categories.$curr_cat->title}}</strong>
            {{if $check_item_categories.$curr_cat->desc}}
              &ndash; {{$check_item_categories.$curr_cat->desc}}
            {{/if}}
          </th>
        </tr>
      {{/if}}
      <tr>
        <td style="padding-left: 1em;" class="text">
          {{mb_value object=$curr_type field=title}}<br />
          <small style="text-indent: 1em; color: #666;">{{mb_value object=$curr_type field=desc}}</small>
        </td>
        <td class="text">
          {{$curr_type->_answer}}
        </td>
      </tr>
      {{/if}}
      {{assign var=category_id value=$curr_type->category_id}}
    {{foreachelse}}
      <tr>
        <td colspan="3">{{tr}}CDailyCheckItemType.none{{/tr}}</td>
      </tr>
    {{/foreach}}
    
    {{if $check_list->object_class != "COperation" || $check_list->type == "postop"}}
    <tr>
      <td colspan="10">
        <strong>Commentaires:</strong><br />
        {{mb_value object=$check_list field=comments}}
      </td>
    </tr>
    {{/if}}
    
    <tr>
      <td colspan="10">
        <strong>Validé par {{mb_value object=$check_list field=validator_id}}</strong> 
      </td>
    </tr>
  </table>
  
  
{{else}}

<script type="text/javascript">
confirmCheckList = function(form) {
  return checkForm(form) &&
    confirm('Tous les points ont-ils été bien vérifiés ?') && 
    onSubmitFormAjax(form, {onComplete: function(){
      {{if $check_list->object_class != "COperation"}}
        location.reload();
      {{/if}}
    } });
}

refreshCheckList{{$check_list->type}} = function(id){
  if (!$("systemMsg").select(".warning, .error").length) {
    $("{{$check_list->type}}-title").down("img").src = "images/icons/tick.png";
    var url = new Url("dPsalleOp", "httpreq_vw_check_list");
    url.addParam("check_list_id", id);
    url.requestUpdate("{{$check_list->type}}");
  }
  else {
    var form =  getForm("edit-CDailyCheckList-{{$check_list->object_class}}-{{$check_list->object_id}}-{{$check_list->type}}");
    if (!$V(form.daily_check_list_id)) {
      $V(form.daily_check_list_id, id);
    }
  }
}

Main.add(function(){
  prepareForm('edit-CDailyCheckList-{{$check_list->object_class}}-{{$check_list->object_id}}-{{$check_list->type}}');
});
</script>

<form name="edit-CDailyCheckList-{{$check_list->object_class}}-{{$check_list->object_id}}-{{$check_list->type}}" method="post" action="?" onsubmit="return confirmCheckList(this)">
  <input type="hidden" name="dosql" value="do_daily_check_list_aed" />
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="daily_check_list_id" value="{{$check_list->_id}}" />
  <input type="hidden" name="object_class" value="{{$check_list->object_class}}" />
  <input type="hidden" name="object_id" value="{{$check_list->object_id}}" />
  <input type="hidden" name="type" value="{{$check_list->type}}" />
  <input type="hidden" name="date" value="{{$check_list->date|ternary:$check_list->date:"now"}}" />
  
  {{if $check_list->object_class == "COperation"}}
    <input type="hidden" name="callback" value="refreshCheckList{{$check_list->type}}" />
  {{/if}}

  {{if $check_list->object_class != "COperation"}}
    {{if !$check_list->_id}}
      <div class="small-info">Veuillez effectuer la vérification journalière pour <strong>{{$check_list->_ref_object}}</strong> grâce au formulaire suivant.</div>
    {{else}}
      <div class="small-info">Veuillez signer la vérification avec un mot de passe correct.</div>
    {{/if}}
  {{/if}}

  <table class="main tbl">
    {{assign var=category_id value=0}}
    {{foreach from=$check_list->_ref_item_types item=curr_type}}
      {{assign var=curr_cat value=$curr_type->category_id}}
      {{if array_key_exists($curr_cat, $check_item_categories)}}
      {{if $curr_type->category_id != $category_id}}
        <tr>
          <th colspan="3" class="text category" style="text-align: left;">
            <strong>{{$check_item_categories.$curr_cat->title}}</strong>
            {{if $check_item_categories.$curr_cat->desc}}
              &ndash; {{$check_item_categories.$curr_cat->desc}}
            {{/if}}
          </th>
        </tr>
      {{/if}}
      <tr>
        <td style="padding-left: 1em;" class="text">
          {{mb_value object=$curr_type field=title}}<br />
          <small style="text-indent: 1em; color: #666;">{{mb_value object=$curr_type field=desc}}</small>
        </td>
        <td>
          {{assign var=attr value=$curr_type->attribute}}
          <label style="white-space: nowrap;">
            <input type="radio" name="_items[{{$curr_type->_id}}]" value="1" {{if $curr_type->_checked == 1}}checked="checked"{{/if}} />
            {{tr}}Yes{{/tr}}
          </label>
          <label style="white-space: nowrap;">
            <input type="radio" name="_items[{{$curr_type->_id}}]" value="0" {{if $curr_type->_checked === null || $curr_type->_checked != 1}}checked="checked"{{/if}} />
            {{if (!$attr || $attr == "normal") || $attr == "notrecommended"}}
              {{tr}}No{{/tr}}
            {{else}}
              {{tr}}N/A{{/tr}}
            {{/if}}
          </label>
          {{if $attr == "notrecommended"}}
          <br />
          <label style="white-space: nowrap;">
            <input type="radio" name="_items[{{$curr_type->_id}}]" value="" {{if $curr_type->_checked === null}}checked="checked"{{/if}} />
            {{tr}}N/R{{/tr}}
          </label>
          {{/if}}
        </td>
      </tr>
      {{/if}}
      {{assign var=category_id value=$curr_type->category_id}}
    {{foreachelse}}
      <tr>
        <td colspan="3">{{tr}}CDailyCheckItemType.none{{/tr}}</td>
      </tr>
    {{/foreach}}
    
    {{if $check_list->object_class != "COperation" || $check_list->type == "postop"}}
    <tr>
      <td colspan="10">
        {{mb_label object=$check_list field=comments}}<br />
        {{mb_field object=$check_list field=comments}}
      </td>
    </tr>
    {{/if}}
    
    <tr>
      <td colspan="10" class="button">
        <select name="validator_id" class="notNull ref" style="width: 10em;">
          <option value="" disabled="disabled" selected="selected">&mdash; Validateur</option>
          
          {{if $check_list->object_class == "COperation"}}
          <optgroup label="Praticiens">
            {{assign var=_obj value=$check_list->_ref_object}}
            <option value="{{$_obj->_ref_chir->user_id}}">{{$_obj->_ref_chir}}</option>
            {{if $_obj->anesth_id}}
            <option value="{{$_obj->_ref_anesth->user_id}}">{{$_obj->_ref_anesth}}</option>
            {{/if}}
          </optgroup>
          {{/if}}
          
          <optgroup label="Personnel">
          {{foreach from=$personnel item=curr_personnel}}
            {{assign var=curr_user value=$curr_personnel->_ref_user}}
            <option value="{{$curr_user->_id}}" {{if $app->user_id == $curr_user->_id}}selected="selected"{{/if}}>{{$curr_user->_view}}</option>
          {{/foreach}}
          </optgroup>
          
        </select>
        <input type="password" class="notNull str" size="10" maxlength="32" name="_validator_password" />
        <button type="button" class="tick" onclick="this.form.onsubmit()">Signer</button>
      </td>
    </tr>
  </table>

</form>

{{/if}}