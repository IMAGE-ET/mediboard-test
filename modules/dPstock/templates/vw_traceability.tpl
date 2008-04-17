<table class="main tbl">
  <tr>
    <th>Produit</th>
    {{foreach from=$tracked_classes item=class}}
    <th>{{tr}}{{$class}}{{/tr}}</th>
    {{/foreach}}
  </tr>
  {{foreach from=$track item=curr_track key=code}}
  <tr>
    <td>{{$code}}</td>
    {{foreach from=$tracked_classes item=class}}
    <td>
      {{if $track.$code.$class}}
      {{foreach from=$curr_track.$class item=item}}
        {{mb_value object=$item field=date}} | 
        {{mb_value object=$item field=_view}}<br />
      {{/foreach}}
      {{/if}}
    </td>
    {{foreachelse}}
    <td>{{$class}}</td>
    {{/foreach}}
  </tr>
  {{/foreach}}
</table>