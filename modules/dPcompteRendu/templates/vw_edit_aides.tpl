
<script type="text/javascript">
Main.add(function () {
  Control.Tabs.create('tab-modules', false);
});

function editAide(aide_id, classname, field, user_id) {
  var url = new Url("dPcompteRendu","edit_aide");
  url.addParam("dialog", 1);
  url.addParam("aide_id", aide_id);
  url.addParam("class", classname);
  url.addParam("field", field);
  url.addParam("user_id", user_id);
  url.redirect();
}

function editAideCallback(id) {
  editAide(id, {{$aide->class|@json}}, {{$aide->field|@json}}, {{$user->_id|@json}});
}

function changeUser(user_id) {
  var oFormc = getForm("change_user");
  var oForm = getForm("editAides");
  
  if(oForm.elements.aide_id.value == '') {
    oFormc.text.value = unescape(encodeURIComponent(oForm.text.value));
  }
  
  oFormc.user_id.value = user_id;
  oFormc.action+="&user_id="+user_id+"&class="+{{$aide->class|@json}}+"&field="+{{$aide->field|@json}};
  oFormc.submit();
}

</script>

{{mb_label object=$aide field="user_id"}}
<form name="change_user" action="?m=dPcompteRendu&a=edit_aide&dialog=1" method="post">
  <input type="hidden" name="text" value="" />
  <select name="user_id" class="{{$aide->_props.user_id}}" onchange="changeUser(this.value);">
    <option value="">&mdash; {{tr}}CAideSaisie.select-user{{/tr}} &mdash;</option>
    {{foreach from=$listPrat item=curr_prat}}
      <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $user->_id}} selected="selected" {{/if}}>
        {{$curr_prat}}
      </option>
    {{/foreach}}
  </select>
</form>

<ul id="tab-modules" class="control_tabs">
  <li>
    <a href="#edit">
      {{if !$aide_id}}{{tr}}CAideSaisie.create{{/tr}}{{else}}{{tr}}CAideSaisie.modify{{/tr}}{{/if}}
    </a>
  </li>
  <li>
    <a href="#list" {{if $aides|@count == 0}}class="empty"{{/if}}>
      Liste des aides
      <small>({{$aides|@count}})</small>
    </a>
  </li>
</ul>

<hr class="control_tabs" />

<div id="edit" style="display: none;">
  <form name="editAides" action="?" method="post" class="{{$aide->_spec}}" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_aide_aed" />
  <input type="hidden" name="m" value="dPcompteRendu" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="callback" value="editAideCallback" />
  
  {{mb_key object=$aide}}
  {{mb_field object=$aide field="class" hidden=1 prop=""}}
  {{mb_field object=$aide field="field" hidden=1 prop=""}}
  
  <table class="form">
    <tr>
      <td colspan="2">
        <button class="new" type="button" onclick="editAide('','{{$aide->class}}','{{$aide->field}}', '{{$user->_id}}')">Création d'une nouvelle aide</button>
      </td>
    </tr>
    
    <tr>
      <th><label title="{{tr}}CAideSaisie-user_id-desc{{/tr}}" for="user_id">{{tr}}CAideSaisie-user_id{{/tr}}</label></th>
      <td>
        <input type="hidden" name="user_id" value="{{$fields.user_id}}" />
        <label>
          <input type="radio" name="_owner_id" value="{{$fields.user_id}}" onclick="$V(this.form.user_id, this.value); $(this.form.user_id).fire('ui:change')"
          {{if (!$aide->_id && $choicepratcab == "prat" && $aide->user_id) || ($aide->_id && $aide->user_id)}}checked="checked"{{/if}} />
          {{$user->_ref_user}}
        </label>
      </td>
    </tr>
    
    <tr>
      <th><label title="{{tr}}CAideSaisie-function_id-desc{{/tr}}" for="function_id">{{tr}}CAideSaisie._owner.func{{/tr}}</label></th>
      <td>
        <input type="hidden" name="function_id" value="{{$fields.function_id}}" />
        <label>
          <input type="radio" name="_owner_id" value="{{$fields.function_id}}" onclick="$V(this.form.function_id, this.value); $(this.form.function_id).fire('ui:change')"
          {{if (!$aide->_id && $choicepratcab == "cab" && $aide->function_id) || ($aide->_id && $aide->function_id)}}checked="checked"{{/if}} />
          {{$user->_ref_function}}
        </label>
      </td>
    </tr>
    
    <tr>
      <th><label title="{{tr}}CAideSaisie-group_id-desc{{/tr}}" for="group_id">{{tr}}CAideSaisie-group_id{{/tr}}</label></th>
      <td>
        <input type="hidden" name="group_id" value="{{$fields.group_id}}" />
        <label>
          <input type="radio" name="_owner_id" value="{{$fields.group_id}}" onclick="$V(this.form.group_id, this.value); $(this.form.group_id).fire('ui:change')"
          {{if (!$aide->_id && $choicepratcab == "group" && $aide->group_id) || ($aide->_id && $aide->group_id)}}checked="checked"{{/if}} />
          {{$group}}
        </label>
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$aide field="class"}}</th>
      <td>{{tr}}{{$aide->class}}{{/tr}}</td>
    </tr>
  
    <tr>
      <th>{{mb_label object=$aide field="field"}}</th>
      <td>{{tr}}{{$aide->class}}-{{$aide->field}}{{/tr}}</td>
    </tr>
  
    {{if array_key_exists('depend_value_1', $dependValues)}}
    <tr>
      <th>{{mb_label object=$aide field="depend_value_1"}}</th>
      <td>
        <select name="depend_value_1" class="{{$aide->_props.depend_value_1}}">
          <option value="">&mdash; Tous</option>
          {{foreach from=$dependValues.depend_value_1 key=_value item=_translation}}
          <option value="{{$_value}}" {{if $_value == $aide->depend_value_1}}selected="selected"{{/if}}>{{$_translation}}</option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    {{/if}}
  
    {{if array_key_exists('depend_value_2', $dependValues)}}
    <tr>
      <th>{{mb_label object=$aide field="depend_value_2"}}</th>
      <td>
        <select name="depend_value_2" class="{{$aide->_props.depend_value_2}}">
          <option value="">&mdash; Tous</option>
          {{foreach from=$dependValues.depend_value_2 key=_value item=_translation}}
          <option value="{{$_value}}" {{if $_value == $aide->depend_value_2}}selected="selected"{{/if}}>{{$_translation}}</option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    {{/if}}
    
    <tr>
      <th>{{mb_label object=$aide field="name"}}</th>
      <td>{{mb_field object=$aide field="name"}}</td>
    </tr>
  
    <tr>
      <th>{{mb_label object=$aide field="text"}}</th>
      <td>{{mb_field object=$aide field="text" rows="4"}}</td>
    </tr>
  
    <tr>
      <td class="button" colspan="2">
        <button class="submit" type="submit">{{if $aide_id}}{{tr}}Save{{/tr}}{{else}}{{tr}}Create{{/tr}}{{/if}}</button>
        {{if $aide_id}}
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'aide à la saisie',objName:'{{$aide->_view|smarty:nodefaults|JSAttribute}}', callback: function(){ getForm('editAides').onsubmit(); /*editAide('','{{$aide->class}}','{{$aide->field}}', '{{$user->_id}}'); */}})">{{tr}}Delete{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
  
  </form>
</div>

<div id="list" style="display: none;">
  <table class="tbl">
    <tr>
      <th/>
      <th>{{mb_label object=$aide field="name"}}</th>
      <th>{{mb_label object=$aide field="text"}}</th>
      <th>{{mb_label object=$aide field="depend_value_1"}}</th>
      <th>{{mb_label object=$aide field="depend_value_2"}}</th>
    </tr>
    {{foreach from=$aides item=_aide}}
      <tr>
        <td class="text">
          {{if $_aide->user_id}}
            <img src="images/icons/user.png" title="{{$user->_view}}"/>
          {{/if}}
          {{if $_aide->function_id}}
            <img src="images/icons/user-function.png" title="{{$user->_ref_function->_view}}"/>
          {{/if}}
          {{if $_aide->group_id}}
            <img src="images/icons/group.png" title="{{$group->_view}}"/>
          {{/if}}
        </td>
        <td class="text">
          <a href="#1" onclick="editAide('{{$_aide->_id}}', '{{$_aide->class}}', '{{$_aide->field}}', '{{$user->_id}}');">
            <span>{{mb_value object=$_aide field="name"}}</span>
          </a>
        </td>
        <td class="text">
          {{mb_value object=$_aide field="text"}}
        </td>
        <td class="text">
          {{mb_value object=$_aide field="depend_value_1"}}
        </td>
        <td class="text">
          {{mb_value object=$_aide field="depend_value_2"}}
        </td>
      </tr>
    {{foreachelse}}
    <tr>
      <td colspan="5"><em>{{tr}}CAideSaisie.none{{/tr}}</em></td>
    </tr>
    {{/foreach}}
  </table>
</div>