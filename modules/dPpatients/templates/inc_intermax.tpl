<!-- Always load it for easier result handler redefinition -->
{{mb_include_script path="includes/javascript/intermax.js"}}

{{if $app->user_prefs.GestionFSE}}

{{if !$app->user_prefs.InterMaxDir}}
<div class="big-warning">
  {{tr}}pref-InterMaxDir-undef{{/tr}}
  <br />
  Voir <strong>Preferences</strong> 
  &gt; <strong>{{tr}}module-dPcabinet-long{{/tr}}</strong>
  &gt; <strong>{{tr}}pref-InterMaxDir{{/tr}}</strong>.
 
</div>
{{/if}}
<script type="text/javascript">

Intermax.errors = {
  "0"   : "{{tr}}Intermax.error.0{{/tr}}",
  "100" : "{{tr}}Intermax.error.100{{/tr}}",
  "110" : "{{tr}}Intermax.error.110{{/tr}}",
  "-30" : "{{tr}}Intermax.error.30{{/tr}}",
  "-35" : "{{tr}}Intermax.error.35{{/tr}}",
  "other": "{{tr}}Intermax.error.other{{/tr}}"
}
</script>


<!-- Yoplet to write Intermax file -->
<applet 
  name="intermaxTrigger"
  code="org.yoplet.Yoplet.class" 
  archive="includes/applets/yoplet.jar?build={{$version.build}}" 
  {{if $debug=="true"}}
  width="400" 
  height="200"
  {{else}}
  width="0" 
  height="0"
  {{/if}}
>

  <param name="action" value="sleep" />
  <param name="lineSeparator" value="---" />
  <param name="debug" value={{if $debug=="true"}}"true"{{else}}"false"{{/if}} />
  <param name="filePath" value="{{$app->user_prefs.InterMaxDir}}/INTERMAX/INTERMAX.INI" />
  <param name="flagPath" value="{{$app->user_prefs.InterMaxDir}}/INTERMAX/CALL.FLG" />
</applet>


<!-- Yoplet to read Intermax file -->
<applet 
  name="intermaxResult"
  code="org.yoplet.Yoplet.class" 
  archive="includes/applets/yoplet.jar?build={{$version.build}}" 
  {{if $debug=="true"}}
  width="400" 
  height="200"
  {{else}}
  width="0" 
  height="0"
  {{/if}}
>
  <param name="action" value="sleep" />
  <param name="lineSeparator" value="---" />
  <param name="debug" value={{if $debug=="true"}}"true"{{else}}"false"{{/if}} />
  <param name="filePath" value="{{$app->user_prefs.InterMaxDir}}/INTERMAX/INTERMAX.OUT" />
<!-- <param name="flagPath" value="{{$app->user_prefs.InterMaxDir}}/INTERMAX/RETURN.FLG" /> -->
</applet>

{{/if}}