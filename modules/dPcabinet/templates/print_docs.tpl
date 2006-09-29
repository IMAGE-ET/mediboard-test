{{if !$documents|@count}}
  <strong>Il n'y a pas de document pour cette consultation</strong>
{{else}}
    </td>
  </tr>
</table>
{{foreach from=$documents item=curr_doc}}
  {{$curr_doc->source|smarty:nodefaults}}
  <br style="page-break-after: always;" />
{{/foreach}}
<table class="main">
  <tr>
    <td>
{{/if}}