{{if $conf.locale_warn}}

<!-- Locales warns -->

{{if !$ajax}}
<div class="small-warning" id="UnlocDiv" style="display: none; position: fixed; bottom: 10px; right: 10px;">
  {{tr}}system-msg-unlocalized_warning{{/tr}} : 
  <div style="text-align: center; font-size: 200%;"><strong>-</strong> !<br />

  <button class="change" type="button" onclick="Localize.showForm();">
    {{tr}}system-button-unlocalized_fix{{/tr}}
  </button>

  <button class="cancel notext opacity-10" type="button" onclick="if (confirm($T('UnlocDiv-AYRRS'))) if (confirm($T('UnlocDiv-RRRS'))) $('UnlocDiv').hide(); ">
    {{tr}}Close{{/tr}}
  </button>
  </div>
</div>

<form action="?m={{$m}}" name="UnlocForm" style="display: none" method="post" class="prepared" onsubmit="return Localize.onSubmit(this);">

<input type="hidden" name="{{$actionType}}" value="{{$action}}" />
<input type="hidden" name="m" value="dPdeveloppement" />
<input type="hidden" name="dosql" value="do_translate_aed" />

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

</form> 
{{/if}}

<script type="text/javascript">
Localize.populate({{$app|static:unlocalized|@json}});
</script>

{{/if}}