{{if !$fintab}}
<table class="tabview">
  <tr>
    <td>
      <table class="tabmenu" cellspacing="0">
        <tr>
          {{foreach from=$tabs item=currTabBox key=keyTabBox}}
          {{if $tab == $currTabBox[0]}}
            {{assign var="sel" value="selected"}}
          {{else}}
            {{assign var="sel" value="normal"}}
          {{/if}}
          <td class="{{$sel}}Left" />
          <td class="{{$sel}}"><a href="?m={{$m}}&amp;tab={{$currTabBox[0]}}">{{tr}}{{$currTabBox[1]}}{{/tr}}</a></td>
          <td class="{{$sel}}Right" />
          {{/foreach}}
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class='tabox'>
{{else}}
    </td>
  </tr>
</table>
{{/if}}