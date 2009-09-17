<script type="text/javascript">
confirmCheckList = function(form) {
	return checkForm(form) && confirm('Tous les points ont-ils été bien vérifiés ?') && onSubmitFormAjax(form, {onComplete: function(){location.reload()} });
}
Main.add(function(){
  prepareForm('edit-CDailyCheckList');
  if (window.updater) {
    updater.stop();
  }
});
</script>

<form name="edit-CDailyCheckList" method="post" action="?" onsubmit="return confirmCheckList(this)">
  <input type="hidden" name="dosql" value="do_daily_check_list_aed" />
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="daily_check_list_id" value="{{$check_list->_id}}" />
  <input type="hidden" name="object_class" value="{{$check_list->object_class}}" />
  <input type="hidden" name="object_id" value="{{$check_list->object_id}}" />
  <input type="hidden" name="date" value="now" />
  
  <input type="hidden" name="del" value="0" />

  {{if !$check_list->_id}}
    <div class="small-info">Veuillez effectuer la vérification journalière de la salle grâce au formulaire suivant.</div>
	{{else}}
    <div class="small-info">Veuillez signer la vérification avec un mot de passe correct.</div>
	{{/if}}
	
	<table class="main tbl">
		<tr>
      <th style="width: 0.1%;" class="title">{{mb_title class=CDailyCheckItem field=checked}}</th>
			<th class="title">{{tr}}CDailyCheckItem{{/tr}}</th>
		</tr>
    {{assign var=category_id value=0}}
		{{foreach from=$check_list->_ref_item_types item=curr_type}}
      {{if $curr_type->category_id != $category_id}}
        {{assign var=curr_cat value=$curr_type->category_id}}
        <tr>
          <th colspan="3" class="category" style="text-align: left;">{{$check_item_categories.$curr_cat}}</th>
        </tr>
      {{/if}}
		  <tr>
		  	<td style="text-align: center;">
				  <input type="checkbox" name="_items[]" value="{{$curr_type->_id}}" {{if @$curr_type->_checked}}checked="checked"{{/if}} />
			  </td>
		  	<td>
		  		<strong>{{mb_value object=$curr_type field=title}}</strong><br />
					<small>{{mb_value object=$curr_type field=desc}}</small>
	      </td>
		  </tr>
      {{assign var=category_id value=$curr_type->category_id}}
		{{foreachelse}}
		  <tr>
		  	<td colspan="3">{{tr}}CDailyCheckItemType.none{{/tr}}</td>
		  </tr>
		{{/foreach}}
    <tr>
      <td colspan="10">
        {{mb_label object=$check_list field=comments}}<br />
        {{mb_field object=$check_list field=comments}}
      </td>
    </tr>
	</table>

  <select name="validator_id" class="notNull ref">
    <option value="" disabled="disabled" selected="selected">&mdash; Validateur</option>
    {{foreach from=$personnel item=curr_personnel}}
		  {{assign var=curr_user value=$curr_personnel->_ref_user}}
      <option value="{{$curr_user->_id}}" {{if $app->user_id == $curr_user->_id}}selected="selected"{{/if}}>{{$curr_user->_view}}</option>
    {{/foreach}}
  </select>
  <input type="password" class="notNull str" size="10" maxlength="32" name="_validator_password" />
  <button type="submit" class="tick">Signer</button>
</form>
