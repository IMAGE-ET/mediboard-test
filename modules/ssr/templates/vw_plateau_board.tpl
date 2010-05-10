{{mb_include_script module="ssr" script="planning"}}
{{mb_include_script module="ssr" script="planification"}}

<script type="text/javascript">
Main.add(function () {
  Planification.showWeek();
  tabs = Control.Tabs.create('tabs-plateaux', true);
	tabs.activeLink.onmousedown();
});

onCompleteShowWeek = function() {
  tabs.activeLink.onmousedown();
}

PlateauxIds = {{$plateaux_ids|@json}};
</script>

<div id="week-changer" style="height: 30px; margin: 0 100px"></div>

<ul id="tabs-plateaux" class="control_tabs">
	{{foreach from=$plateaux item=_plateau}}
  <li>
  	<a href="#{{$_plateau->_guid}}" onmousedown="PlanningEquipement.showMany(PlateauxIds['{{$_plateau->_id}}']);">
  		{{$_plateau}}
		</a>
	</li>
	{{/foreach}}
</ul>

<hr class="control_tabs" />

{{foreach from=$plateaux item=_plateau}}
<div id="{{$_plateau->_guid}}" style="display: none;">
	{{foreach from=$_plateau->_back.equipements item=_equipement}}
	<div id="planning-equipement-{{$_equipement->_id}}" style="margin: 0 5px; float: left; width: 400px; height: 295px;">
		{{$_equipement}}
	</div>
	{{/foreach}}
</div>
{{/foreach}}
