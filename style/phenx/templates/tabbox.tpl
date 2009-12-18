{{if !$fintab}}
<table id="tabmenu" cellspacing="0">
  <tr>
    {{foreach from=$tabs item=currTabBox key=keyTabBox}}
      {{if $tab == $currTabBox}}
        {{assign var="sel" value="selected"}}
      {{else}}
        {{assign var="sel" value="normal"}}
      {{/if}}
	    <td class="{{$sel}}Left"></td>
	    <td class="{{$sel}}"><a href="?m={{$m}}&amp;tab={{$currTabBox}}">
	      {{if $currTabBox==="configure"}}
          {{tr}}{{$currTabBox}}{{/tr}}
        {{else}}
          {{tr}}mod-{{$m}}-tab-{{$currTabBox}}{{/tr}}        
        {{/if}}
	    </a></td>
	    <td class="{{$sel}}Right"></td>
    {{/foreach}}
  </tr>
</table>

<div class="content">
{{/if}}

