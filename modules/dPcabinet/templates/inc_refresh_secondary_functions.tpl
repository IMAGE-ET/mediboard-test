{{if $chir->_id && $chir->isPraticien()}}
  <select name="_function_secondary_id" style="width: 15em;">
      <option value="{{$chir->function_id}}">{{$chir->_ref_function}}</option>
    {{foreach from=$_functions item=_function}}
      <option value="{{$_function->function_id}}">{{$_function}}</option>
    {{/foreach}}
  </select>
{{else}}
  {{tr}}CConsultation-choose_prat{{/tr}}
{{/if}}