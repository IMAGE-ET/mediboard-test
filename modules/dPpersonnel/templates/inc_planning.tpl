<script type="text/javascript">
	tableau_periode={{$tableau_periode|@json}};
	nombreelem = {{$tableau_periode|@json}}.length;
	changedate = function (sens) {
  var choix = {{$choix|@json}};
  var form = getForm("searchplanning");
	
  var date_courante = Date.fromDATE(form.elements.date_debut.value); 
  
  if (choix=="semaine") {
    if(sens=='p') {
      date_courante.addDays(-7);
    }
    else if(sens=='n'){
      date_courante.addDays(7);
    }
  }
  else {
    if(sens == "p") {
      date_courante.setMonth(date_courante.getMonth() - 1);
    }
    else if(sens=='n'){
      date_courante.setMonth(date_courante.getMonth() + 1);
    }
  }
  form.elements.date_debut.value = date_courante.toDATE();
  loadPlanning(form);
}
</script>
<table class="main">
  <tr>
    <!-- Navigation par semaine ou mois-->
    <td colspan="2">
      <button class="left" onclick="changedate('p')" style="float: left;">
        {{if $choix=="semaine"}}{{tr}}Previous week{{/tr}}{{else}}{{tr}}Previous month{{/tr}}{{/if}}
      </button>
      <button class="right rtl" onclick="changedate('n')" style="float: right;">
        {{if $choix=="semaine"}}{{tr}}Next week{{/tr}}{{else}}{{tr}}Next month{{/tr}}{{/if}}
      </button>
    </td>
  </tr>
  <tr>
    <!-- Affichage : semaine du tant au tant -->
    <th colspan="{{$tableau_periode|@count}}" style="text-align:center; font-size:14pt">
      {{if $choix=="semaine"}}
        {{$choix}} du {{$tableau_periode.0|date_format:"%d %B %Y"}} au
        {{$tableau_periode.6|date_format:"%d %B %Y"}}
      {{else}}
         {{$tableau_periode.0|date_format:"%B %Y"}}
      {{/if}}
    </th>
  </tr>
  <tr>
    <td>
    <!-- Affichage du planning -->
   <table id="schedule">
     <tr style="height: 2em;">
       <td style="width: 12em;"></td>
       {{foreach from=$tableau_periode item=_periode}}
         {{assign var=day value=$_periode|date_format:"%A"|upper|substr:0:1}}
         <th {{if $day == "S" || $day == "D"}}style="background: #ddf;"{{/if}}>
      	   <big>{{$day}}</big>
      	   <br/>{{$_periode|date_format:"%d"}}
         </th>
       {{/foreach}}
     </tr>
     <!-- Zone d'insertion des plages de conge-->
     {{assign var="indice" value="-1"}}
     {{assign var="count" value="-1"}}
     {{foreach from=$plagesconge item=_plage1}}
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
         {{foreach from=$plagesconge item=_plage2}}
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
      {{foreach from=$tableau_periode item=_periode name=td_list}}
        {{if !$smarty.foreach.td_list.first}}
          {{assign var=day value=$_periode|date_format:"%A"|upper|substr:0:1}}
          <td {{if $day == "S" || $day == "D"}}style="background: #ddf;"{{/if}}></td>
        {{/if}}
      {{/foreach}}
      </tr>
      {{/if}}
      {{foreachelse}}
      <tr>
        <td colspan="{{math equation="x+1" x=$tableau_periode|@count}}">
          {{tr}}CPlageConge.none{{/tr}}
        </td> 
      </tr>
      {{/foreach}}
      </table>   
    </td>
  </tr>
</table>