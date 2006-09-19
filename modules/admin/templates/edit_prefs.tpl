{{if !$tab}}
<form name="changeuser" action="./index.php?m=admin&amp;a={{$a}}" method="post">
{{else}}
<form name="changeuser" action="./index.php?m=admin&amp;tab={{$tab}}" method="post">
{{/if}}
<input type="hidden" name="dosql" value="do_preference_aed" />
<input type="hidden" name="pref_user" value="{{$user_id}}" />
<input type="hidden" name="del" value="0" />

{{if $tab && $canEdit && $user_id}}
<a href="index.php?m={{$m}}&amp;tab=edit_prefs&amp;user_id=0" class="buttonedit">
  Editer les Préférences par Défaut
</a>
{{/if}}
<table class="form">
  <tr>
    <th colspan="2" class="title">
      {{tr}}User Preferences{{/tr}}: 
      {{if $user_id}}
        {{$user->_view}}
      {{else}}
        {{tr}}Default{{/tr}}
      {{/if}}
    </th>
  </tr>
  <tr>
    <th>
      {{tr}}Locale{{/tr}}
    </th>
    <td>
      <select name="pref_name[LOCALE]" class="text" size="1">
        {{foreach from=$locales item=currLocale key=keyLocale}}
        <option value="{{$keyLocale}}" {{if $keyLocale==$prefs.LOCALE}}selected="selected"{{/if}}>
          {{tr}}{{$currLocale}}{{/tr}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <th>
      {{tr}}User Interface Style{{/tr}}
    </th>
    <td>
      <select name="pref_name[UISTYLE]" class="text" size="1">
        {{foreach from=$styles item=currStyles key=keyStyles}}
        <option value="{{$keyStyles}}" {{if $keyStyles==$prefs.UISTYLE}}selected="selected"{{/if}}>
          {{tr}}{{$currStyles}}{{/tr}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <td class="button" colspan="2">
      <button type="submit" class="submit">
        {{tr}}submit{{/tr}}
      </button>
    </td>
  </tr>
</table>
</form>