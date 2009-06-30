{{if !$fintab}}
    <td class="tabview">
      <table class="tabmenu" cellspacing="0">
        <tr>
          {{foreach from=$tabs item=currTabBox key=keyTabBox}}
          {{if $tab == $currTabBox[0]}}
            {{assign var="sel" value="selected"}}
          {{else}}
            {{assign var="sel" value=""}}
          {{/if}}
          <td class="{{$sel}} left">
            [
          </td>
          <td class="{{$sel}}"><a href="?m={{$m}}&amp;tab={{$currTabBox[0]}}">
            {{if $currTabBox[1]===null}}
              {{tr}}mod-{{$m}}-tab-{{$currTabBox[0]}}{{/tr}}            
            {{else}}
              {{tr}}{{$currTabBox[1]}}{{/tr}}
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