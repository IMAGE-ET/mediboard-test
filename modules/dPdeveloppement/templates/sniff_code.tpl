{{mb_script module=developpement script=code_sniffer}}

<script type="text/javascript">
Main.add(function () {
  PairEffect.initGroup('tree-content');
});
</script>

<div id="sniff-run" style="display: none; width: 800px;">
  <h1>
    Sniff reports status
    <br />
    uptodate: <span id="uptodate">&mdash;</span>,
    obsolete: <span id="obsolete">&mdash;</span>,
    none:     <span id="none"    >&mdash;</span>,
    index:    <span id="index"   >&mdash;</span>,
    duration: <span id="duration">&mdash;</span>
  </h1>
  <div id="sniff-list" style="height: 200px; overflow: auto">
    <table class="tbl">
      <tr>
        <th>Path</th>
        <th style="width: 8em;">Status</th>
      </tr>
      <tbody class="files">
      </tbody>
    </table>
  </div>
  <table class="tbl">
    <tr>
      <td class="button" colspan="2">
        <label><input type="checkbox" class="auto" checked="checked" onclick="CodeSniffer.setAuto(this)"/>{{tr}}Auto{{/tr}}</label>
        <label><input type="checkbox" class="force" onclick="CodeSniffer.setForce(this)" />Force</label>
        <button class="change" onclick="CodeSniffer.start()">{{tr}}Start{{/tr}}</button>
        <button class="cancel" onclick="CodeSniffer.close()">{{tr}}Close{{/tr}}</button>
      </td>
    </tr>
  </table>
  
  <div id="sniff-file" style="height: 300px; overflow: auto">
  </div>
</div>

{{mb_include template=tree_sniffed_files dir=mediboard basename=mediboard files=$files}}
