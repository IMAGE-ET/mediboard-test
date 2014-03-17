{{*assign var=showLastUpdate value=$app->user_prefs.showLastUpdate*}}
{{assign var=showLastUpdate value=false}}

<span class="release-info">
  {{if $applicationVersion.releaseCode}}
    {{if $showLastUpdate}}
      <a href="#1" onclick="App.showReleaseInfo();return false;" title="{{$applicationVersion.title}}">
        Branche : {{$applicationVersion.releaseTitle|capitalize}}
      </a>
    {{else}}
      <label title="{{$applicationVersion.title}}">
        Branche : {{$applicationVersion.releaseTitle|capitalize}}
      </label>
    {{/if}}
  {{else}}
    {{tr}}Latest update{{/tr}}
    <label title="{{$applicationVersion.title}}" {{if in_array($applicationVersion.relative.unit, array("second", "minute", "hour"))}}style="font-weight: bold"{{/if}}>
     {{$applicationVersion.relative.count}} {{tr}}{{$applicationVersion.relative.unit}}{{if $applicationVersion.relative.count > 1}}s{{/if}}{{/tr}}
    </label>
  {{/if}}
</span>