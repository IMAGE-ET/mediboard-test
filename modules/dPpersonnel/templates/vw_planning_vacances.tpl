{{* $Id:*}}

{{*
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}
<script type="text/javascript">

var nombreelem = {{$tableau_periode|@json}}.length;
var tableau_periode={{$tableau_periode|@json}};
var largeur_nom = 100;

function display_plage(plage_id, debut, fin) {
  var width = parseInt($("schedule").getWidth()) - largeur_nom;
	var plage = $("plage" + plage_id);
	var width_calc = (fin * (width/nombreelem).floor());
	var margin_calc = 0 ;
  if(((debut*width/nombreelem).ceil()) < 0) {
		margin_calc = -(debut*width/nombreelem).ceil();
	}
	plage.setStyle({
	  left: (debut*width/nombreelem).ceil()+'px',
    width: width_calc - 2 +'px',
  });
	
	plage.down(".content").setStyle({
	  marginLeft: Math.max(2, margin_calc)+'px'
	}); 
}

function changedate(sens) {
  var choix = {{$choix|@json}};
	var form = getForm("planning");
  var date_courante = Date.fromDATE(form.elements.date_debut.value); 
	
	if (choix=="semaine") {
	  if(sens == "p") {
	    date_courante.addDays(-7);
		}
		else {
		  date_courante.addDays(7);
		}
	}
	else {
	  if(sens == "p") {
	    date_courante.setMonth(date_courante.getMonth() - 1);
		}
		else {
		  date_courante.setMonth(date_courante.getMonth() + 1);
		}
	}
	form.elements.date_debut.value = date_courante.toDATE();
	
	form.submit();
}

function movesnap(x, y, drag) {
  var table = $("schedule");
  
  var columns = table.down("tr").next().select("td");
	var left, found = false;
	var widthsave = columns[0].getWidth();
	
	var leftOffsets = [];
	var tableLeft = table.cumulativeOffset().left + largeur_nom;
	columns.each(function(col){
	 leftOffsets.push(col.cumulativeOffset().left)+largeur_nom;
	});
  //leftOffsets.shift();
	if(x > 0) {
	leftOffsets.each(function(offset){
	  
		if (found) return;
		left = offset - tableLeft;
		if (left >= x) {
		  found = true;
		  return;
	  }
	});
	if (left < x) {
		left = left + widthsave - 5;
	}
	}
	else {
  leftOffsets.each(function(offset){
    if (found) return;
    left = offset - parseInt(table.getWidth()-largeur_nom) + widthsave - tableLeft;
    if (left >= x) {
      found = true;
      return;
    }
  }); 
  }
	
	drag.element.down().setStyle({
	  marginLeft: Math.abs(Math.min(left, 2))+"px"
	});
	
	return [left,0];
}

function DragDropPlage(draggable){
  var element = draggable.element;
	var decalage = parseInt(element.style.left);
	var widthtotal = parseInt($("schedule").getWidth()) - largeur_nom;
	var taille = (widthtotal / nombreelem).round();
	var new_left = (decalage / taille).round();
	var widthplage = (parseInt(element.style.width) / taille).round() - 1;
	var datedeb = tableau_periode[0];
	
	var date_debut = Date.fromDATE(datedeb);
	date_debut.addDays(new_left);
	
	var date_fin = date_debut;
	date_debut = date_debut.toDATE();

	date_fin.addDays(widthplage);
	date_fin = date_fin.toDATE();
	var plage_id = element.id.substring(5);

	var url = new Url("dPpersonnel", "do_plagevac_aed");
  url.addParam("plage_id", plage_id);
	url.addParam("date_debut", date_debut);
	url.addParam("date_fin", date_fin);
	url.requestUpdate("systemMsg", {
	  getParameters: {m: 'dPpersonnel', a: 'do_plagevac_aed'},
	  method: "post",
		// Si l'enregistremet de la plage échoue, il faut replacer la plage à sa place antérieure
		onComplete: function(){
		  if (detecterror()) {
				oldDrag.drag.element.style.left = parseInt(oldDrag.left)+"px";
			}
	  }
	});
}

function savePosition(drag){
  window.oldDrag = {
	  left: drag.element.style.left,
		drag: drag
  };
}

function detecterror(){
  return $("systemMsg").select(".error").length > 0;
}
</script>

<style type="text/css">

#schedule {
  table-layout:    fixed;
  width:           100%;
  border-spacing:  0px;
  border-collapse: collapse;
	overflow:        hidden;
	border: 1px solid #ddd;
	position: relative;
}

#schedule td, 
#schedule th {
  border:   1px solid #ddd;
}

.ligne {
  height:  50px;
}

.plage {
  height:           40px;
  background-color: #ccc;
  position:         absolute;
	-moz-border-radius: 3px;
  -webkit-border-radius: 3px;
  border-radius: 3px;
  border:           2px solid #aaa;
  z-index:          0;
	overflow: visible;
	padding: 2px 0;
	margin-top: 1px;
}

.plage .content {
  margin: 2px;
}

.insertion {
  position: relative;
	
}

.nom {
	margin-top: -1px;
	z-index: 1;
	position :relative;
	background-color: #fff;
	height: 50px;
	line-height: 2em;
	text-align: left;
}
</style>

<table class="main">
	<tr>
		<td colspan=2>
			<form name="planning" action="?" method="get">
        <input type="hidden" name="m" value="{{$m}}"/>
				<input type="hidden" name="tab" value="{{$tab}}"/>
				<table class="form" style="width:30%">
					<tr>
						<th>{{mb_label class=CPlageVacances field="date_debut"}}</th>
						<td>{{mb_field object=$filter field="date_debut" form="planning" register=true}}</td>
					</tr>	
					<tr>
						<th>{{tr}}CPlageVacances-choix-periode{{/tr}}</th>
						<td>
							<label>
							  <input type="radio" name="choix" {{if $choix=="semaine"}}checked="checked"{{/if}} value="semaine" /> Semaine
						  </label>
							<label>
							  <input type="radio" name="choix" {{if $choix=="mois"}}checked="checked"{{/if}} value="mois" /> Mois
					    </label>
					  </td>
					</tr>
					<tr>
						<td colspan="2" style="text-align:center" >
							<button type="submit" class="search">{{tr}}Filter{{/tr}}</button>
						</td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
  <!-- Affichage : semaine du tant au tant -->
	<tr>
		<th colspan="{{$tableau_periode|@count}}" style="text-align:center; font-size:14pt">
			{{if $choix=="semaine"}}
			  {{$choix}} du {{$tableau_periode.0|date_format:"%d %B %Y"}} au
		    {{$tableau_periode.6|date_format:"%d %B %Y"}}
			{{else}}
			   {{$tableau_periode.0|date_format:"%B %Y"}}
			{{/if}}
    </th>
	</tr>
	<!-- Navigation par semaine ou mois-->
	<tr>
		<td colspan=2>
	     <button class="left" onclick="changedate('p')" style="float: left;">
			   {{if $choix=="semaine"}}{{tr}}Previous week{{/tr}}{{else}}{{tr}}Previous month{{/tr}}{{/if}}
			 </button>
     	 <button class="right" onclick="changedate('n')" style="float: right;">
         {{if $choix=="semaine"}}{{tr}}Next week{{/tr}}{{else}}{{tr}}Next month{{/tr}}{{/if}}
			 </button>
	  </td>
  </tr>
	<tr>
		<td>
		  <!-- Affichage du planning -->
			<table id="schedule">
				<tr style="height:30px;">
					<td style="width: 100px"></td>
				  {{foreach from=$tableau_periode item=_periode}}
				  <th>{{$_periode|date_format:"%a"}}<br/>{{$_periode|date_format:" %d"}}</th>
				  {{/foreach}}
				</tr>
		   	<!-- Zone d'insertion des plages de vacances-->
				{{assign var="indice" value="-1"}}
				{{assign var="count" value="-1"}}
				{{foreach from=$plagesvac item=_plage1}}
			  {{if $indice != $_plage1->user_id}}
				{{assign var="userid" value=$_plage1->user_id}}
        {{assign var="indice" value=$userid}}
        {{assign var="count" value=$count+1}}
				<tr class="ligne">
					<th>
					   <div class="nom">
						{{assign var=mediuser value=$_plage1->_ref_user}}
             {{mb_include module=mediusers template=inc_vw_mediuser object=$mediuser nodebug=true}}
						 </div>
					</th>
				  <td>
				  	<div class="insertion">
				  	{{foreach from=$plagesvac item=_plage2}}
						  {{if $_plage2->user_id == $indice}}
							  <div id = "plage{{$_plage2->_id}}" class = "plage">
                  <div class="content">
                     {{$_plage2->_duree}}
										{{if $_plage2->_duree == 1}}
									    {{tr}}day{{/tr}}
										{{else}}
										  {{tr}}days{{/tr}}
										{{/if}}
                    <br/>
                    <span onmouseover="ObjectTooltip.createEx(this, '{{$_plage2->_guid}}')">
                    {{$_plage2->libelle}}
                    </span>
				              <script type="text/javascript">
				                Main.add(function(){
				                  display_plage({{$_plage2->_id}},{{$_plage2->_deb}},{{$_plage2->_fin}});
				                  new Draggable('plage{{$_plage2->_id}}', {constraint:"horizontal", snap: movesnap, onStart: savePosition, onEnd: DragDropPlage});
				                  
				                  Event.observe(window, "resize", function(){
				                    display_plage({{$_plage2->_id}},{{$_plage2->_deb}},{{$_plage2->_fin}});
				                  });
				                });
				               </script>
                    </div>
                  </div>
							{{/if}}
						{{/foreach}}
						</div>
				  </td>
				  {{foreach from=$tableau_periode item=td name=td_list}}
            {{if !$smarty.foreach.td_list.first}}
              <td></td>
            {{/if}}
          {{/foreach}}
        </tr>
			  {{/if}}
			  {{/foreach}}
	    </table>	 
	  </td>
	</tr>
</table>

