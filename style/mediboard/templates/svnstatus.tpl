{{if $svnStatus}}
<a href="tmp/svnlog.txt" target="_blank" title="{{$svnStatus.date|date_format:$dPconfig.datetime}} (Révision: {{$svnStatus.revision}})">
  {{tr}}Latest update{{/tr}}
   <span {{if in_array($svnStatus.relative.unit, array("second", "minute", "hour"))}}style="font-weight: bold"{{/if}}>
     {{$svnStatus.relative.count}} {{tr}}{{$svnStatus.relative.unit}}{{if $svnStatus.relative.count > 1}}s{{/if}}{{/tr}}
   </span>
</a>
{{/if}}
