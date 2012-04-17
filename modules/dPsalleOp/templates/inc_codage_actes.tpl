{{assign var="do_subject_aed" value="do_planning_aed"}}
{{assign var="object" value=$subject}}

{{mb_include module=salleOp template=js_codage_ccam}}

<script type="text/javascript">
  Main.add (function () {
    Control.Tabs.create('codage_tab_group', true);
  });
</script>

<ul id="codage_tab_group" class="control_tabs">
  {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam == "1"}}
    <li><a href="#ccam_tab">CCAM</a></li>
    <li><a href="#ngap_tab">NGAP</a></li>
  {{/if}}
  {{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed == "1"}}
    <li><a href="#tarmed_tab">TARMED</a></li>
    <li><a href="#caisse_tab">Caisses</a></li>
  {{/if}}
</ul>

<hr class="control_tabs" />

<div id="ccam_tab" style="display:none">
  <div id="ccam">
    {{mb_include module=salleOp template=inc_codage_ccam}}
  </div>
</div>

<div id="ngap_tab" style="display:none">
  <div id="listActesNGAP">
    {{mb_include module=cabinet template=inc_codage_ngap}}
  </div>
</div>
{{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed == "1"}}
  <div id="tarmed_tab" style="display:none">
    <div id="listActesTarmed">
      {{mb_include module=tarmed template=inc_codage_tarmed}}
    </div>
  </div>
  <div id="caisse_tab" style="display:none">
    <div id="listActesCaisse">
      {{mb_include module=tarmed template=inc_codage_caisse}}
    </div>
  </div>
{{/if}}