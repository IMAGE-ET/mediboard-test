{{mb_script module=dPdeveloppement script=code_sniffer}}

<script type="text/javascript">
Main.add(function () {
  PairEffect.initGroup('tree-content');
});
</script>

<div id="sniff-run" style="display: none; width: 800px;">
  <div id="sniff-list" style="height: 200px; overflow: auto">
    <table class="tbl">
      <tr>
        <th>Files to go <small class="count">(&mdash;)</span></th>
        <th style="width: 8em;">Status</th>
      </tr>
      <tbody class="files">
      </tbody>
    </table>
  </div>
  <table class="tbl">
    <tr>
      <td class="button" colspan="2">
        <label><input type="checkbox" class="auto" onclick="CodeSniffer.setAuto(this)" />{{tr}}Auto{{/tr}}</label>
        <button class="change" onclick="CodeSniffer.start()">{{tr}}Start{{/tr}}</button>
        <button class="cancel" onclick="CodeSniffer.close()">{{tr}}Close{{/tr}}</button>
      </td>
    </tr>
  </table>
  
  <div id="sniff-file" style="height: 300px; overflow: auto">
  </div>
</div>

{{mb_include template=tree_sniffed_files dir=mediboard basename=mediboard files=$files}}
