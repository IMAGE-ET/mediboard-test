{{mb_script module=hospi script=modele_etiquette}}

<script>
Main.add(ModeleEtiquette.refreshList);
</script>

<table class="main">
  <tr>
    <td style="width: 50%;">
      {{mb_include template=inc_filter_etiquettes}}
      <div id="list_etiq"></div>
    </td>
  </tr>
</table>