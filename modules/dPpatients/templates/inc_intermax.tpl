<!-- Always load it for easier result handler redefinition -->
{{mb_include_script script=intermax}}
{{assign var=debug value="false"}}
{{if $app->user_prefs.GestionFSE}}

{{if !$app->user_prefs.InterMaxDir && !$app->user_prefs.VitaleVision}}
  <div class="small-warning">
    {{tr}}pref-InterMaxDir-undef{{/tr}}<br />
    Voir <strong>Preferences &gt; {{tr}}module-dPcabinet-long{{/tr}} &gt; {{tr}}pref-InterMaxDir{{/tr}}</strong>.
  </div>
{{/if}}

<script type="text/javascript">
Intermax.autoWatch = {{$dPconfig.dPpatients.intermax.auto_watch}};
</script>

<!-- Yoplet to write Intermax file -->
<!-- Ne pas mettre "display: none" sinon l'applet ne se lancera pas dans Firefox -->
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
  style="position: absolute; left: 5000px;"
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
  style="position: absolute; left: 5000px;"
  {{/if}}
>
  <param name="action" value="sleep" />
  <param name="lineSeparator" value="---" />
  <param name="debug" value={{if $debug=="true"}}"true"{{else}}"false"{{/if}} />
  <param name="filePath" value="{{$app->user_prefs.InterMaxDir}}/INTERMAX/INTERMAX.OUT" />
  {{if $dPconfig.dPpatients.intermax.auto_watch}}
	<param name="flagPath" value="{{$app->user_prefs.InterMaxDir}}/INTERMAX/RETURN.FLG" />
  {{/if}}
</applet>

{{/if}}