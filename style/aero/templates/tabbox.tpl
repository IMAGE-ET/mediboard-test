{{if !$fintab}}
<ul class="tabmenu">
  {{foreach from=$tabs item=currTabBox key=keyTabBox}}
  <li {{if $tab == $currTabBox}} class="selected" {{/if}}>
    <a href="?m={{$m}}&amp;tab={{$currTabBox}}">
      {{if $currTabBox === "configure"}}
        {{tr}}{{$currTabBox}}{{/tr}}
      {{else}}
        {{tr}}mod-{{$m}}-tab-{{$currTabBox}}{{/tr}}        
      {{/if}}
    </a>
  </li>
  {{/foreach}}
</ul>
{{/if}}
