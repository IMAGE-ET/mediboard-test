{{mb_include_script module="dPpatients" script="pat_selector"}}

<script type="text/javascript">
  function IPPconflict(){
    var url = new Url("dPpatients", "ajax_ipp_conflicts");
    url.requestUpdate("ipp-conflicts");
  }

  onMergeComplete = function() {
	  IPPconflict();
  }
	  
  Main.add(Control.Tabs.create.curry('tabs-identito-vigilance', true));
  
  Main.add(function () {
	  IPPconflict();  
	});
</script>

<ul id="tabs-identito-vigilance" class="control_tabs">
  <li><a href="#similar">Patients similaires</a></li>
  <li>
    <a {{if $count_matching_patients == 0}}class="empty"{{/if}} 
      href="#matching">Patients identiques ({{$count_matching_patients}})</a>
  </li>
  <li>
    <a class="{{if $count_conflicts == 0}}empty{{else}}wrong{{/if}}" 
      href="#ipp-conflict">IPP conflits({{$count_conflicts}})</a>
  </li>
</ul>

<hr class="control_tabs" />

<div id=matching style="display: none;">
  {{mb_include template=inc_matching_patients}}
</div>

<div id="similar" style="display: none;">
  {{mb_include template=inc_similar_patients}}
</div>

<div id="ipp-conflict" style="display: none;">
  <table class="tbl" id="ipp-conflicts">
  </table>
</div>