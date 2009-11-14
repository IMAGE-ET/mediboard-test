{{if !$fintab}}
    <td class="tabview">
      <table class="tabmenu" cellspacing="0">
        <tr>
          {{foreach from=$tabs item=currTabBox key=keyTabBox}}
          {{if $tab == $currTabBox}}
            {{assign var="sel" value="selected"}}
          {{else}}
            {{assign var="sel" value=""}}
          {{/if}}
          <td class="{{$sel}} left">
            [
          </td>
          <td class="{{$sel}}"><a href="?m={{$m}}&amp;tab={{$currTabBox}}">
            {{if $currTabBox==="configure"}}
              {{tr}}{{$currTabBox}}{{/tr}}
            {{else}}
              {{tr}}mod-{{$m}}-tab-{{$currTabBox}}{{/tr}}        
            {{/if}}
          </a></td>
          <td class="{{$sel}} right">
            ]
          </td>
          {{/foreach}}
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="tabox">
{{/if}}