{{mb_script module=developpement script=code_sniffer}}

<script type="text/javascript">
  // Too many IDs to warn duplicates
  Element.warnDuplicates = Prototype.emptyFunction;
  Main.add(PairEffect.initGroup.curry('tree-content'));
</script>

<div id="sniff-run" style="display: none; width: 900px; height: 800px;">
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

<script type="text/javascript">
  Main.add(ViewPort.SetAvlHeight.curry('tree-files', 1));
  Main.add(ViewPort.SetAvlHeight.curry('tree-types', 1));
</script>

<div style="width: 45%; float: left;">
  <h1>
    Rapport des erreurs par fichier
    ({{$count}} fichiers)
  </h1>
  <div id="tree-files">
    {{mb_include template=tree_sniffed_files dir=mediboard basename=mediboard files=$files}}
  </div>
</div>

<div style="width: 45%; float: right;">
  <h1>Rapport des erreurs par type</h1>
  {{if !$existing_count}}
    <div class="small-info">
      No code sniffing reports available.
    </div>
  {{elseif !count($types)}}
    <div class="small-success">
      No single warning found!
    </div>
  {{else}}
    <div id="tree-types">
      {{mb_include template=tree_error_types dir=mediboard type=mediboard item=$types}}
    </div>
  {{/if}}
</div>
