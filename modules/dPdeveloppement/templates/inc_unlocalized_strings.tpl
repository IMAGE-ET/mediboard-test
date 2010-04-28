{{assign var=unloc_count value=$app|static:unlocalized|@count}}

{{if $dPconfig.locale_warn && $unloc_count}}

<!-- Local warns -->
<script>
Localize = {
  showForm: function() {
	  var form = getForm('UnlocForm');
    form.show();
    form.scrollTo();
  },
  onSubmit: function(form) {
    return onSubmitFormAjax(form, { onComplete: location.reload.bind(location) } );
  }
}
</script>

<div class="small-warning">
  {{tr}}dPdeveloppement-msg-unlocalized_warning{{/tr}} : <strong>{{$unloc_count}}</strong>... 
	<button class="change" type="button" onclick="Localize.showForm();">
		{{tr}}dPdeveloppement-button-unlocalized_fix{{/tr}}
	</button>
</div>

<form action="?m={{$m}}" name="UnlocForm" style="display: none" method="post" class="prepared" onsubmit="return Localize.onSubmit(this);">

<input type="hidden" name="{{$actionType}}" value="{{$action}}" />
<input type="hidden" name="m" value="dPdeveloppement" />
<input type="hidden" name="dosql" value="do_translate_aed" />

<table class="form">
  
  <tr>
    <th>Language</th>
    <td><input type="text" readonly="readonly" name="language" value="{{$app->user_prefs.LOCALE}}" /></td>
  </tr>

  <tr>
    <th>Module</th>
    <td>
      <select name="module">
        {{foreach from=$modules key=module_name item=_module}}
          <option value="{{$module_name}}" {{if $module_name == $m}} selected="selected" {{/if}}>
            {{$_module}}
          </option>
        {{/foreach}}
      </select>
    </td>
  </tr>

  {{foreach from=$app|static:unlocalized key=_unlocalized item=whatever}}
  <tr>
    <th>{{$_unlocalized}} </th>
    <td><input size="70" type="text" name="tableau[{{$_unlocalized}}]" value="" /></td>
  </tr>
  {{/foreach}}

  <tr>
    <td class="button" colspan="10">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>

</table>  
{{/if}}