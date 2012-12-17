{{if $conf.locale_warn}}

<!-- Locales warns -->

{{if !$ajax}}
<form action="?m={{$m}}" name="UnlocForm" style="display: none" method="post" class="prepared" onsubmit="return Localize.onSubmit(this);">

<input type="hidden" name="{{$actionType}}" value="{{$action}}" />
<input type="hidden" name="m" value="dPdeveloppement" />
<input type="hidden" name="dosql" value="do_translate_aed" />

<div style="height: 400px; overflow-y: scroll;">

<table class="form">
  <tr>
    <th class="title" colspan="2">{{tr}}system-title-unlocalized{{/tr}}</th>
  </tr>
  <tr>
    <th>{{tr}}Language{{/tr}}</th>
    <td><input type="text" readonly="readonly" name="language" value="{{$app->user_prefs.LOCALE}}" /></td>
  </tr>

  <tr>
    <th>{{tr}}Module{{/tr}}</th>
    <td>
      <select name="module">
        <option value="common">&mdash; common</option>
        {{foreach from=$modules key=module_name item=_module}}
        <option value="{{$module_name}}" {{if $module_name == $m}} selected="selected" {{/if}}>
          {{$_module}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>

  <tbody>
  </tbody>

  <tr>
    <td class="button" colspan="10">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      <button class="cancel" type="button" onclick="Control.Modal.close();">{{tr}}Close{{/tr}}</button>
    </td>
  </tr>

</table> 

</div>

</form> 
{{/if}}

<script type="text/javascript">
Main.add(Localize.populate.curry({{$app|static:unlocalized|@json}}));
</script>

{{/if}}