{{assign var="do_subject_aed" value="do_planning_aed"}}
{{assign var="object" value=$subject}}

{{mb_include module=dPsalleOp template=js_codage_ccam}}

<script type="text/javascript">
  Main.add (function () {
    Control.Tabs.create('codage_tab_group', true);
  });
</script>

<ul id="codage_tab_group" class="control_tabs">
  <li><a href="#ccam_tab">CCAM</a></li>
  <li><a href="#ngap_tab">NGAP</a></li>
</ul>

<hr class="control_tabs" />

<div id="ccam_tab" style="display:none">
  <div id="ccam">
    {{mb_include module=dPsalleOp template=inc_codage_ccam}}
  </div>
</div>

<div id="ngap_tab" style="display:none">
  <div id="listActesNGAP">
    {{mb_include module=dPcabinet template=inc_codage_ngap}}
  </div>
</div>