<!-- Always load it for easier result handler redefinition -->
{{mb_include_script path="includes/javascript/intermax.js"}}
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

Intermax.errors = {
  // Intégration Mediboard LogicMax
  "100" : "{{tr}}Intermax.error.100{{/tr}}",
  "110" : "{{tr}}Intermax.error.110{{/tr}}",
  "120" : "{{tr}}Intermax.error.120{{/tr}}",

	// Pas d'erreur
  "200" : "{{tr}}Intermax.error.200{{/tr}}",

  // Manipulation des FSE
  "0" : "{{tr}}Intermax.error.0{{/tr}}",
  "-20" : "{{tr}}Intermax.error.20{{/tr}}",
  "-21" : "{{tr}}Intermax.error.21{{/tr}}",
  "-22" : "{{tr}}Intermax.error.22{{/tr}}",
  "-23" : "{{tr}}Intermax.error.23{{/tr}}",
  "-24" : "{{tr}}Intermax.error.24{{/tr}}",
  "-25" : "{{tr}}Intermax.error.25{{/tr}}",
  "-26" : "{{tr}}Intermax.error.26{{/tr}}",
  "-29" : "{{tr}}Intermax.error.29{{/tr}}",
  
  // Utilisation des CPS
  "-30" : "{{tr}}Intermax.error.30{{/tr}}",
  "-31" : "{{tr}}Intermax.error.31{{/tr}}",
  "-32" : "{{tr}}Intermax.error.32{{/tr}}",
  "-33" : "{{tr}}Intermax.error.33{{/tr}}",
  "-34" : "{{tr}}Intermax.error.34{{/tr}}",
  "-35" : "{{tr}}Intermax.error.35{{/tr}}",
  "-36" : "{{tr}}Intermax.error.36{{/tr}}",
  "-37" : "{{tr}}Intermax.error.37{{/tr}}",

	// Gestion des spécialités
  "-40" : "{{tr}}Intermax.error.40{{/tr}}",
  "-41" : "{{tr}}Intermax.error.41{{/tr}}",

	// Administration de LogicMax
  "-50" : "{{tr}}Intermax.error.50{{/tr}}",
  "-51" : "{{tr}}Intermax.error.51{{/tr}}",
  "-52" : "{{tr}}Intermax.error.52{{/tr}}",
  "-53" : "{{tr}}Intermax.error.53{{/tr}}",
  "-54" : "{{tr}}Intermax.error.54{{/tr}}",

  "-60" : "{{tr}}Intermax.error.60{{/tr}}",
  
  "-70" : "{{tr}}Intermax.error.70{{/tr}}",
  "-71" : "{{tr}}Intermax.error.71{{/tr}}",
  "-72" : "{{tr}}Intermax.error.72{{/tr}}",
  "-73" : "{{tr}}Intermax.error.73{{/tr}}",
  "-74" : "{{tr}}Intermax.error.74{{/tr}}",
  "-75" : "{{tr}}Intermax.error.75{{/tr}}",
  "-76" : "{{tr}}Intermax.error.76{{/tr}}",
  
  // Erreur non répertoriée  
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
  {{if $dPconfig.dPpatients.intermax.auto_watch}}
	<param name="flagPath" value="{{$app->user_prefs.InterMaxDir}}/INTERMAX/RETURN.FLG" />
  {{/if}}
</applet>

{{/if}}