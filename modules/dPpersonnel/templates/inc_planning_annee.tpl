<style type="text/css">
.cal td {
	text-align: center;
}

.cal td.empty {
  background: #fff;
}

.cal td.occuped {
	background-color: #ccc;
	font-weight: bold;
}

.cal td.occuped.start {
  border-left: 2px solid #999;
}

.cal td.occuped.end {
  border-right: 2px solid #999;
}
</style>

<script type="text/javascript">

changemode = function(type, date, user_id) {
  var form = getForm("searchplanning");
  $V(form.choix, type);
	var champs = date.split('-');
	$V(form.date_debut_da,champs[2] + "/" + champs[1] + "/" + champs[0]);
	$V(form.date_debut, date);
	$V(form.user_id, user_id);
	loadPlanning(form);
}
changeannee = function (sens) {
  var choix = {{$choix|@json}};
  var form = getForm("searchplanning");
  
  var date_courante = Date.fromDATE(form.elements.date_debut.value); 
  if(sens=='p') {
    date_courante.addYears(-1);
  }
  else if(sens=='n'){
    date_courante.addYears(1);
  }
  
  form.elements.date_debut.value = date_courante.toDATE();
  loadPlanning(form);
}
</script>
<table class="main">
  <td colspan="2">
    <button class="left" onclick="changeannee('p')" style="float: left;">
      {{tr}}Previous year{{/tr}}
    </button>
    <button class="right rtl" onclick="changeannee('n')" style="float: right;">
      {{tr}}Next year{{/tr}}
    </button>
  </td>
</table>
<table class="main">
{{assign var="k" value=1}}
{{foreach from=1|range:12 item=j}}

{{assign var="start" value=$tab_start.$k}}
{{assign var="k" value=$k+1}}
{{assign var="duree" value=$tab_start.$k}}
{{assign var="k" value=$k+1}}
 {{if $j%6==1 }}
 <tr>
{{/if}}
 <td>
   <table class="tbl cal">
   	<tr>
   		{{assign var=day value="01"}}
      {{assign var=month value=$j|pad:2:"0"}}
			{{assign var=year value=$debut_periode|date_format:"%Y"}}
      {{assign var=date value="$year-$month-$day"}}
   	  <th colspan="7" class="title">
				<a href="#" onclick="changemode('mois','{{$date}}',{{$filter->user_id}})">{{$date|date_format:"%B %Y"}}</a>
			</th>
		</tr>
		 <tr>
		 	{{foreach from=1|range:7 item=_j}}
			{{assign var=date_model value="2010-02-$_j"}}
			<th>{{$date_model|date_format:"%A"|upper|substr:0:1}}</th>
			{{/foreach}}
		 </tr>
		 {{if $duree+$start > 36}}
		   {{assign var=longueur value=42}}
		 {{elseif $duree+$start < 30}}
		   {{assign var=longueur value=28}}
		 {{else}}
		   {{assign var=longueur value=35}}
		 {{/if}}
		 {{foreach from=1|range:$longueur item=i}}
			 {{if $i%7 == 1 }}
		 <tr>
		 	 {{/if}}
			 {{if $i>=$start && $i<=$duree+$start-1}}
			 {{assign var=tday value=$i-$start+1}}
		 	 {{assign var=day value=$tday|pad:2:"0"}}
       {{assign var=month value=$j|pad:2:"0"}}
		 	 {{assign var=date value="$year-$month-$day"}}
			 {{assign var=open value=0}}
			 {{foreach from=$plagesconge item=_plage}}
			   {{assign var=date_debut value=$_plage->date_debut}}
				 {{assign var=date_fin value=$_plage->date_fin}}
				 {{if $date>=$date_debut && $date<=$date_fin }}
					 {{assign var=open value=1}}
				<td class="occuped {{if $date == $date_debut}}start{{/if}} {{if $date == $date_fin}}end{{/if}}" title="{{$_plage}}">
				 {{/if}}
			 {{/foreach}}
			 {{if !$open}}
			   <td>
			 {{/if}}
				 {{assign var=jour value=$i-$start+1}}
				   <a href="#Week-{{$date}}" onclick="changemode('semaine','{{$date}}',{{$filter->user_id}})">{{$jour}}</a>
			  </td>
			{{else}}
			  <td class="empty"></td>
			{{/if}}
		 	{{if $i%7==0}}
			</tr>
			 {{/if}}
		 {{/foreach}}
	  </table>
   </td>
{{if $j%6==0 }}
  </tr>
{{/if}}
{{/foreach}}
</table>