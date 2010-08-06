{{mb_include_script module=ssr script=planning}}
{{mb_include_script module=ssr script=planification}}

<script type="text/javascript">

Main.add(function () {
  Control.Tabs.create("tabs_plannings_kines");
});

showPlanningOffline = function(kine_id, kine_guid, type){
  $(kine_guid+'-'+type).down('.week-container').setStyle({height: '600px' });
  (function(){ 
    if(type == "tech"){
		  window['tab-'+kine_id].setActiveTab('planning_technicien_'+kine_id); 
		}
    window['planning-'+kine_guid+'-'+type].updateEventsDimensions();
  }).defer();
}

</script>

<h1 style="text-align: center;">
  {{tr}}Week{{/tr}} {{$date|date_format:'%U'}},
  {{assign var=month_min value=$monday|date_format:'%B'}}
  {{assign var=month_max value=$sunday|date_format:'%B'}}
  {{$month_min}}{{if $month_min != $month_max}}-{{$month_max}}{{/if}}
  {{$date|date_format:'%Y'}}
</h1>

<ul id="tabs_plannings_kines" class="control_tabs">
	{{foreach from=$plannings key=kine_id item=_planning}}
	  {{assign var=kine value=$kines.$kine_id}}
		<li onmouseup="showPlanningOffline('{{$kine->_id}}', '{{$kine->_guid}}', 'tech');">
			<a href="#planning_{{$kine_id}}">{{$kine->_view}}</a>
		</li>
  {{/foreach}}
<hr class="control_tabs" />

<br />
{{foreach from=$plannings key=kine_id item=_planning}}
  {{assign var=kine value=$kines.$kine_id}}

  <div id="planning_{{$kine_id}}" style="display: none;">
	  <script type="text/javascript">
			Main.add(function () {
			  window['tab-{{$kine_id}}'] = Control.Tabs.create("tabs_plannings_select_{{$kine_id}}");
			});
		</script>

	  <ul id="tabs_plannings_select_{{$kine_id}}" class="control_tabs small">
	    <li><a href="#planning_technicien_{{$kine_id}}">Planning rééducateur</a></li>
			<li onmouseup="showPlanningOffline('{{$kine->_id}}', '{{$kine->_guid}}', 'surv');"><a href="#planning_surveillance_{{$kine_id}}">Planning de surveillance</a></li>
		</ul>
		<hr class="control_tabs" />
		<div id="planning_technicien_{{$kine_id}}" style="display: none;">
      {{$_planning.technicien|smarty:nodefaults}}			
		</div>

  	<div id="planning_surveillance_{{$kine_id}}" style="display: none;">
      {{$_planning.surveillance|smarty:nodefaults}}
    </div>
  </div>
{{/foreach}}	