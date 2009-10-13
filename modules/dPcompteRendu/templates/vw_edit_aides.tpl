<script type="text/javascript">
function submitFormAides(oForm){
  if(checkForm(oForm)){
    submitFormAjax(oForm, 'systemMsg', { onComplete : window.close });
    window.close();
  }
  return false;
}
</script>

<form name="editAides" action="?m=dPcompteRendu" method="post">
<input type="hidden" name="dosql" value="do_aide_aed" />
<input type="hidden" name="m" value="dPcompteRendu" />
<input type="hidden" name="aide_id" value="" />
{{mb_field object=$aide field="class" hidden=1 prop=""}}
{{mb_field object=$aide field="field" hidden=1 prop=""}}
<input type="hidden" name="del" value="0" />

<table class="form">
  <tr>
    <th class="category" colspan="2">
      Création d'une aide
    </th>
  </tr>
  
  <tr>
    <th>{{mb_label object=$aide field="function_id"}}</th>
    <td>
      <select name="function_id" class="{{$aide->_props.function_id}}">
        <option value="">&mdash; Associer à une fonction &mdash;</option>
        {{foreach from=$listFunc item=curr_func}}
          <option class="mediuser" style="border-color: #{{$curr_func->color}};" value="{{$curr_func->function_id}}" {{if $curr_func->function_id == $aide->function_id}} selected="selected" {{/if}}>
            {{$curr_func->_view}}
          </option>
        {{/foreach}}
      </select>
    </td>
  </tr>

  <tr>
    <th>{{mb_label object=$aide field="user_id"}}</th>
    <td>
      <select name="user_id" class="{{$aide->_props.user_id}}">
        <option value="">&mdash; Associer à un utilisateur &mdash;</option>
        {{foreach from=$listPrat item=curr_prat}}
          <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $aide->user_id}} selected="selected" {{/if}}>
            {{$curr_prat->_view}}
          </option>
        {{/foreach}}
      </select>
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
      <button class="submit" type="button" onclick="submitFormAides(this.form)">
        Créer et Fermer
      </button>
    </td>
  </tr>
</table>

</form>