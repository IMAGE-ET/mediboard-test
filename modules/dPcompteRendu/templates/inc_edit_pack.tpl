<script type="text/javascript">
Main.add(Pack.refreshFormModeles);
</script>

{{mb_include template=inc_form_pack}}

{{if $pack->_id}}

<table class="tbl">
  <tr>
    <th class="category" colspan="2">
      {{tr}}CPack-back-modele_links{{/tr}}
    </th>
  </tr>
</table>

<div id="list-modeles-links" style="height: 100px; overflow-y: auto;">

{{mb_include template=inc_list_modeles_links}}

</div>

<table class="form">
  <tr>
    <th class="category" colspan="2">{{tr}}CModeleToPack-msg-create{{/tr}}</th>
  </tr>
  <tr>
    <td colspan="2" style="height: 2em;" id="form-modeles-links"></td>
 </tr>
{{/if}}
