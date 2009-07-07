{{if !$documents|@count}}
  <div class="small-info">
    Il n'y a aucun document pour cette consultation
  </div>
{{else}}
<script type="text/javascript">
Main.add(window.print);
</script>
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