{{* $Id:*}}

{{*
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}
<script type="text/javascript">

tableau_periode = {{$tableau_periode|@json}};
nombreelem = tableau_periode.length;
largeur_nom = 135;

loadPlanning = function(form) {
  var url = new Url("dPpersonnel", "ajax_planning");
  url.addFormData(form);
  url.requestUpdate("planning");
  return false;
}

display_plage = function (plage_id, debut, fin) {
  var width = parseInt($("schedule").getWidth()) - largeur_nom;
  var plage = $("plage" + plage_id);
  var width_calc = (fin * (width/nombreelem).floor());
  var margin_calc = 0;
  
  if((debut*width/nombreelem).ceil() < 0) {
    margin_calc = -(debut*width/nombreelem).ceil();
  }
  plage.setStyle({
    left: (debut*width/nombreelem).ceil()+'px',
    width: width_calc - 2 +'px'
  });
 
  plage.down(".content").setStyle({
    marginLeft: Math.max(2, margin_calc)+'px'
  }); 
}

movesnap = function(x, y, drag) {
  var table = $("schedule");
  var columns = table.down("tr").next().select("td");
  var left, found = false;
  var widthsave = columns[0].getWidth();
  var leftOffsets = [];
  var tableLeft = table.cumulativeOffset().left + largeur_nom;
  
  columns.each(function(col){
    leftOffsets.push(col.cumulativeOffset().left) + largeur_nom;
  });
  
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

  return [left, 0];
}

DragDropPlage = function(draggable){
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
  url.addParam("dosql","do_plagevac_aed");
  url.addParam("date_fin", date_fin);
  url.requestUpdate("systemMsg", {
    method: "post",
    // Si l'enregistrement de la plage échoue, il faut replacer la plage à sa place antérieure
    onComplete: function(){
      if ($("systemMsg").select(".error").length > 0) {
        oldDrag.drag.element.style.left = parseInt(oldDrag.left)+"px";
      } else {
        loadPlanning(getForm("searchplanning"));
        loadUser("{{$filter->user_id}}");
      }
    }
  });
}


savePosition = function(drag){
  window.oldDrag = {
  left: drag.element.style.left,
  drag: drag
  };
}
  
toggleYear = function (form) {
  if($V(form.user_id) == '') {
    form.choix[2].disabled = "disabled";
    $V(form.choix, "mois");
  }
  else {
    form.choix[2].disabled = "";
  }
}

function editPlageVac(plage_id, user_id){
    var url = new Url("dPpersonnel", "vw_idx_plages_vac", "tab");
    url.addParam("plage_id", plage_id);
    url.addParam("user_id", user_id);
    url.redirect();
}

Main.add(function(){
  var form = getForm("searchplanning");
  var choixannee = $('annee');

  loadPlanning(form);

  if($V(form.user_id) == "") {
    choixannee.checked='';
    choixannee.disabled='disabled';
  }
  else {
    choixannee.disabled='';
  }
});
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
  border: 1px solid #ddd;
}

.ligne {
  height: 50px;
}

.plage {
  height:            40px;
  background-color:  #ccc;
  position:          absolute;
 -moz-border-radius: 3px;
  -webkit-border-radius: 3px;
  border-radius: 3px;
  border:  2px solid #aaa;
  z-index: 0;
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
    <td colspan="2">
      <form name="searchplanning" method="get" onsubmit="return loadPlanning(this)">
        <input type="hidden" name="m" value="{{$m}}"/>
        <input type="hidden" name="date_debut" value="{{$filter->date_debut}}"/>
        <table class="form">
        {{if $affiche_nom==1}}
          <tr>
            <th>{{mb_label object=$filter field="user_id"}}</th>
            <td>
               <select name="user_id" onchange="toggleYear(this.form); getForm('searchplanning').onsubmit();">
                 <option value="">{{tr}}CMediusers.all{{/tr}}</option>
                 {{mb_include module=mediusers template=inc_options_mediuser list=$mediusers selected=$filter->user_id}}
               </select>
             </td>
           </tr>
         {{/if}}
         

           <tr>
             <th style="width: 50%;">{{tr}}CPlageVacances-choix-periode{{/tr}}</th>
             <td style="width: 50%;">
               <label>
                 <input onclick="getForm('searchplanning').onsubmit();" type="radio" name="choix" {{if $choix=="semaine"}}checked="checked"{{/if}} value="semaine" /> {{tr}}week{{/tr}}
               </label>
               <label >
                 <input onclick="getForm('searchplanning').onsubmit();" type="radio" name="choix" {{if $choix=="mois"}}checked="checked"{{/if}} value="mois" /> {{tr}}month{{/tr}}
               </label>
               <label >
                 <input onclick="getForm('searchplanning').onsubmit();" id="annee" type="radio" hidden="true" name="choix" {{if $choix=="annee"}}checked="checked"{{/if}} value="annee" /> {{tr}}year{{/tr}}
               </label>
             </td>
           </tr>
        </table>
      </form>
    </td>
  </tr>
  <tr>
    <td id="planning" colspan="2">
    </td>
  </tr>
</table>
