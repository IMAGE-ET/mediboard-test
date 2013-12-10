{{mb_script module=cabinet script=yoplet}}
{{assign var=yoplet_upload_url value=$conf.dPfiles.yoplet_upload_url}}
{{assign var=cookies value=""}}

{{foreach from="; "|explode:$smarty.server.HTTP_COOKIE item=cookie}}
  {{assign var=temp_cookie value='='|explode:$cookie}}
  {{assign var=cookies value="`$cookies` `$temp_cookie.0`"}}
{{/foreach}}

{{if $app->user_prefs.directory_to_watch}}
  <script>
    File.applet.extensions = '{{$conf.dPfiles.extensions_yoplet|lower}} {{$conf.dPfiles.extensions_yoplet|upper}}';
    File.appletDirectory = "{{$app->user_prefs.directory_to_watch|addslashes}}";
  </script>
  
  <!-- Modale pour l'applet -->
  {{mb_include module=files template=yoplet_modal object=$object}}

  <applet
    name="yopletuploader"
    code="org.yoplet.Yoplet.class"
    archive="includes/applets/yoplet.jar?build={{$version.build}}"
    {{if $app->user_prefs.debug_yoplet == 1}}
      width="400"
      height="400"
    {{else}}
      width="1"
      height="1"
    {{/if}}
>
    <param name="action" value="" />
    <param name="debug" value="true" />
    <param name="codebase_lookup" value="false" />
    <param name="permissions" value="all-permissions" />

    {{if $yoplet_upload_url}}
      <param name="url" value="{{$yoplet_upload_url}}/?m=files&a=ajax_yoplet_upload&suppressHeaders=1&dialog=1" />
    {{else}}
      <param name="url" value="{{$base_url}}/?m=files&a=ajax_yoplet_upload&suppressHeaders=1&dialog=1" />
    {{/if}}

    <param name="content" value="a" />
    <param name="cookies" value="{{$app->session_name}} {{$cookies}}" />
    <param name="user_agent" value="{{$smarty.server.HTTP_USER_AGENT}}" />
    <param name="do_callback" value="1" />
  </applet>

  {{if $app->user_prefs.debug_yoplet}}
    <div id="yoplet-debug-console" style="border: 1px solid grey;">
      Directory watched: "{{$app->user_prefs.directory_to_watch}}"<br />
    </div>
  {{/if}}
{{/if}}