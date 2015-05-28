{{mb_default var=list_chirs   value=null}}
{{mb_default var=list_anesths value=null}}
{{mb_default var=preview      value=false}}

{{if $check_list->isReadonly()}}

  <table class="main tbl">
    {{assign var=category_id value=0}}
    {{foreach from=$check_list->_ref_item_types item=curr_type}}
      {{assign var=curr_cat value=$curr_type->category_id}}
      {{if array_key_exists($curr_cat, $check_item_categories)}}
        {{if $curr_type->category_id != $category_id}}
          <tr>
            <th colspan="2" class="text category" style="text-align: left;">
              <strong>{{$check_item_categories.$curr_cat->title}}</strong>
              {{if $check_item_categories.$curr_cat->desc}}
                &ndash; {{$check_item_categories.$curr_cat->desc}}
              {{/if}}
            </th>
          </tr>
        {{/if}}
        <tr>
          <td style="padding-left: 1em;" class="text">
            {{mb_value object=$curr_type field=title}}
            {{if $curr_type->desc}}
              <div class="compact" style="margin-top: 0.5em;">{{mb_value object=$curr_type field=desc}}</div>
            {{/if}}
          </td>
          <td class="text">
            {{$curr_type->_answer}}
          </td>
        </tr>
      {{/if}}
      {{assign var=category_id value=$curr_type->category_id}}
    {{foreachelse}}
      <tr>
        <td colspan="2" class="empty">{{tr}}CDailyCheckItemType.none{{/tr}}</td>
      </tr>
    {{/foreach}}

    {{if !in_array($check_list->object_class, 'CDailyCheckList'|static:_HAS_classes) || $check_list->type == "postop" || $check_list->type == "postendoscopie" || $check_list->type == "postendoscopie_bronchique" || $check_list->type == "postop_radio" || $check_list->type == "disp_vasc_apres" || $check_list->type == "cesarienne_apres" || $check_list->type == "postop_ch"}}
    <tr>
      <td colspan="2">
        <strong>Commentaires:</strong><br />
        {{mb_value object=$check_list field=comments}}
      </td>
    </tr>
    {{/if}}

    <tr>
      <td colspan="2">
        <strong>Validé par {{mb_value object=$check_list field=validator_id}}</strong>
      </td>
    </tr>
  </table>

{{else}}

<script>
var HAS_classes = {{'CDailyCheckList'|static:_HAS_classes|@json}};

confirmCheckList = function(form) {
  return checkForm(form) &&
    confirm('Tous les points ont-ils été bien vérifiés ?') &&
    onSubmitFormAjax(form, {
      onComplete: function(){
        if (HAS_classes.indexOf($V(form.object_class)) == -1 && $V(form.ref_type_list) != "fermeture_salle" && $V(form.ref_type_list) != "fermeture_sspi" && $V(form.ref_type_list) != "fermeture_preop"
        && (($V(form.ref_type_list) != "ouverture_sspi" && $V(form.ref_type_list) != "ouverture_preop") || $V(form.choose_moment_edit) == 0)) {
          location.reload();
        }
      }
    });
}

refreshCheckList{{$check_list->type}}_{{$check_list->list_type_id}} = function(id){
  var form = getForm("edit-CDailyCheckList-{{$check_list->object_class}}-{{$check_list->object_id}}-{{$check_list->type}}-{{$check_list->list_type_id}}");

  if ($V(form.validator_id) && $V(form._validator_password) && !$("systemMsg").select(".warning, .error").length) {
    if ($('{{$check_list->type}}'+'-title')) {
      $('{{$check_list->type}}'+'-title').down("img").src = "images/icons/tick.png";
    }
    else if ($('{{$check_list->list_type_id}}'+'-param-title')) {
      $('{{$check_list->list_type_id}}'+'-param-title').down("img").src = "images/icons/tick.png";
    }
    var url = new Url("dPsalleOp", "httpreq_vw_check_list");
    url.addParam("check_list_id", id);
    url.requestUpdate("check_list_{{$check_list->type}}_{{$check_list->list_type_id}}");
  }
  else {
    if (!$V(form.daily_check_list_id)) {
      $V(form.daily_check_list_id, id);
    }
  }
}

saveCheckListIdCallback{{$check_list->type}}_{{$check_list->list_type_id}} = function(id) {
  var form = getForm("edit-CDailyCheckList-{{$check_list->object_class}}-{{$check_list->object_id}}-{{$check_list->type}}-{{$check_list->list_type_id}}");

  if (!$V(form.daily_check_list_id)) {
    $V(form.daily_check_list_id, id);
  }
}

{{if $preview}}
  submitCheckList = function() {};
{{else}}
  submitCheckList = function(form, quicksave) {
    if (!quicksave) {
      return confirmCheckList(form);
    }

    $V(form._validator_password, "");

    return onSubmitFormAjax(form, {
      check: function(){return true}
    });
  }
{{/if}}

Main.add(function(){
  prepareForm('edit-CDailyCheckList-{{$check_list->object_class}}-{{$check_list->object_id}}-{{$check_list->type}}-{{$check_list->list_type_id}}');
});
</script>

<form name="edit-CDailyCheckList-{{$check_list->object_class}}-{{$check_list->object_id}}-{{$check_list->type}}-{{$check_list->list_type_id}}"
      method="post" action="?" onsubmit="{{if $preview}} return false; {{else}} return submitCheckList(this, false); {{/if}}">
  <input type="hidden" name="dosql" value="do_daily_check_list_aed" />
  <input type="hidden" name="m" value="salleOp" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="daily_check_list_id" value="{{$check_list->_id}}" />
  <input type="hidden" name="object_class" value="{{$check_list->object_class}}" />
  <input type="hidden" name="object_id" value="{{$check_list->object_id}}" />
  <input type="hidden" name="type" value="{{$check_list->type}}" />
  <input type="hidden" name="list_type_id" value="{{$check_list->list_type_id}}" />
  <input type="hidden" name="ref_type_list" value="{{$check_list->_ref_list_type->type}}" />
  <input type="hidden" name="choose_moment_edit" value="{{"dPsalleOp CDailyCheckList choose_moment_edit"|conf:"CGroups-$g"}}" />
  <input type="hidden" name="date" value="{{$check_list->date|ternary:$check_list->date:"now"}}" />
  <input type="hidden" name="group_id" value="{{$check_list->group_id|ternary:$check_list->group_id:$g}}" />

  {{if in_array($check_list->object_class, 'CDailyCheckList'|static:_HAS_classes) ||
  ($check_list->_ref_list_type->type == "fermeture_salle" || $check_list->_ref_list_type->type == "fermeture_sspi" || $check_list->_ref_list_type->type == "fermeture_preop" ||
    ("dPsalleOp CDailyCheckList choose_moment_edit"|conf:"CGroups-$g" && ($check_list->_ref_list_type->type == "ouverture_sspi" || $check_list->_ref_list_type->type == "ouverture_preop")))
  || $check_list->_ref_list_type->type == "intervention"}}
    <input type="hidden" name="callback" value="refreshCheckList{{$check_list->type}}_{{$check_list->list_type_id}}" />
  {{else}}
    <input type="hidden" name="callback" value="saveCheckListIdCallback{{$check_list->type}}_{{$check_list->list_type_id}}" />
  {{/if}}

  {{if !in_array($check_list->object_class, 'CDailyCheckList'|static:_HAS_classes)}}
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
          <th colspan="2" class="text category" style="text-align: left;">
            <strong>{{$check_item_categories.$curr_cat->title}}</strong>
            {{if $check_item_categories.$curr_cat->desc}}
              &ndash; {{$check_item_categories.$curr_cat->desc}}
            {{/if}}
          </th>
        </tr>
      {{/if}}
      <tr>
        <td class="text">
          <ul style="padding-left: 0; list-style-position: inside;">
            <li>{{mb_value object=$curr_type field=title}}</li>
          </ul>

          {{if $curr_type->desc}}
            <div class="compact" style="margin-top: 0.5em;">{{mb_value object=$curr_type field=desc}}</div>
          {{/if}}
        </td>
        <td style="text-align: left;">
          {{assign var=attr value=$curr_type->attribute}}
          {{assign var=default_value value=$curr_type->default_value}}

          {{if $default_value == "yes"}}
            {{mb_include module=salleOp template=inc_check_list_field_yes}}
            {{mb_include module=salleOp template=inc_check_list_field_no}}
          {{else}}
            {{mb_include module=salleOp template=inc_check_list_field_no}}
            {{mb_include module=salleOp template=inc_check_list_field_yes}}
          {{/if}}

          {{if $attr == "notrecommended"}}
            <div>
              {{mb_include module=salleOp template=inc_check_list_field_notrecommended}}
            </div>
          {{/if}}

          {{if $attr == "notapplicable"}}
            <div>
              {{mb_include module=salleOp template=inc_check_list_field_notapplicable}}
            </div>
          {{/if}}
        </td>
      </tr>
      {{/if}}
      {{assign var=category_id value=$curr_type->category_id}}
    {{foreachelse}}
      <tr>
        <td colspan="2" class="empty">{{tr}}CDailyCheckItemType.none{{/tr}}</td>
      </tr>
    {{/foreach}}

    {{if !in_array($check_list->object_class, 'CDailyCheckList'|static:_HAS_classes) || $check_list->type == "postop" || $check_list->type == "postendoscopie" || $check_list->type == "postendoscopie_bronchique" || $check_list->type == "postop_radio" || $check_list->type == "disp_vasc_apres" || $check_list->type == "cesarienne_apres" || $check_list->type == "postop_ch"}}
    <tr>
      <td colspan="2" class="text">
        <hr />
        {{mb_label object=$check_list field=comments}}<br />
        {{mb_field object=$check_list field=comments onchange="submitCheckList(this.form,true)"}}
      </td>
    </tr>
    {{/if}}

    <tr>
      <td colspan="2" class="button">
        <label for="validator_id" style="display: none;">{{tr}}CDailyCheckList-validator_id{{/tr}}</label>
        <select name="validator_id" class="notNull ref" style="width: 10em;">
          <option value="" disabled="disabled" selected="selected">&mdash; Validateur</option>
          {{assign var=type_validateur value='|'|explode:$check_list->_ref_list_type->type_validateur}}

          {{if $check_list->object_class == "COperation" && $check_list->list_type_id && in_array("chir_interv", $type_validateur)}}
            <optgroup label="Praticiens">
              {{assign var=_obj value=$check_list->_ref_object}}
              <option value="{{$_obj->_ref_chir->user_id}}">{{$_obj->_ref_chir}}</option>
              {{if $anesth_id && isset($anesth|smarty:nodefaults)}}
                <option value="{{$anesth->_id}}">{{$anesth}}</option>
              {{/if}}
            </optgroup>
          {{/if}}

          {{if $check_list->object_class == "COperation" && !$check_list->list_type_id}}
            <optgroup label="Praticiens">
              {{assign var=_obj value=$check_list->_ref_object}}
              <option value="{{$_obj->_ref_chir->user_id}}">{{$_obj->_ref_chir}}</option>
              {{if $anesth_id && isset($anesth|smarty:nodefaults)}}
              <option value="{{$anesth->_id}}">{{$anesth}}</option>
              {{/if}}
            </optgroup>
          {{else}}
            {{if $list_chirs && (in_array("chir", $type_validateur) ||
            ($check_list->object_class != "CSalle" && $check_list->object_class != "CBlocOperatoire" && !$check_list->list_type_id))}}
              <optgroup label="Chirurgiens">
                {{mb_include module=mediusers template=inc_options_mediuser list=$list_chirs selected=$app->user_id}}
              </optgroup>
            {{/if}}

            {{if $list_anesths && (in_array("anesth", $type_validateur) ||
            ($check_list->object_class != "CSalle" && $check_list->object_class != "CBlocOperatoire" && !$check_list->list_type_id))}}
              <optgroup label="Anesthésistes">
                {{mb_include module=mediusers template=inc_options_mediuser list=$list_anesths selected=$app->user_id}}
              </optgroup>
            {{/if}}
          {{/if}}
          <optgroup label="Personnel">
            {{if $check_list->object_class == "CSalle" || $check_list->object_class == "CBlocOperatoire" || ($check_list->object_class == "COperation" && $check_list->list_type_id)}}
              {{foreach from=$personnel item=curr_personnel}}
                {{if in_array($curr_personnel->emplacement, $type_validateur)}}
                  {{assign var=curr_user value=$curr_personnel->_ref_user}}
                  <option value="{{$curr_user->_id}}" {{if $app->user_id == $curr_user->_id}}selected="selected"{{/if}}>{{$curr_user->_view}}</option>
                {{/if}}
              {{/foreach}}
            {{else}}
              {{foreach from=$personnel item=curr_personnel}}
                {{assign var=curr_user value=$curr_personnel->_ref_user}}
                <option value="{{$curr_user->_id}}" {{if $app->user_id == $curr_user->_id}}selected="selected"{{/if}}>{{$curr_user->_view}}</option>
              {{/foreach}}
            {{/if}}
          </optgroup>
        </select>
        <label for="_validator_password" style="display: none;">{{tr}}CDailyCheckList-_validator_password{{/tr}}</label>
        <input type="password" class="notNull str" size="10" maxlength="32" name="_validator_password" />
        <button type="button" class="tick" onclick="submitCheckList(this.form)">Signer</button>
      </td>
    </tr>
  </table>

</form>

{{/if}}