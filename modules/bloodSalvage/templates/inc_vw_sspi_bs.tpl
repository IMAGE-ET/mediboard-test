
<script type="text/javascript">
Main.add(function() {
var url = new Url;
      url.setModuleAction("bloodSalvage", "httpreq_total_time");
      url.addParam("blood_salvage_id", "{{$blood_salvage->_id}}");
      url.periodicalUpdate("totaltime", { frequency: 60 });
}

);
</script>
{{mb_include_script module="bloodSalvage" script="bloodSalvage"}}

{{include file=inc_bs_sspi_header.tpl}}

<div id="timing">
  {{include file="inc_vw_bs_sspi_timing.tpl"}}
</div>
<div id="totaltime">
  {{include file="inc_total_time.tpl"}}
</div>
<div id="cell-saver-infos">
  {{include file="inc_vw_cell_saver_volumes.tpl}}
</div>
<div id="materiel">
  {{include file=inc_vw_blood_salvage_sspi_materiel.tpl}}
</div> 
  <!-- CR et FSEI -->
  <div id="incident">
    {{include file=inc_blood_salvage_cr_fsei.tpl}}
  </div>
  <div id="listNurse">
  {{include file=inc_vw_blood_salvage_personnel.tpl}}
</div>