<!-- Yoplet to write file -->

<applet 
  name="intermaxTrigger"
  code="org.yoplet.Yoplet.class" 
  archive="includes/applets/yoplet.jar" 
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


<!-- Yoplet to read file -->
<applet 
  name="intermaxResult"
  code="org.yoplet.Yoplet.class" 
  archive="includes/applets/yoplet.jar" 
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
  <param name="flagPath" value="{{$app->user_prefs.InterMaxDir}}/INTERMAX/RETURN.FLG" />
</applet>
