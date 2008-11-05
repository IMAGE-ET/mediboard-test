<script type="text/javascript">

function printPlanning() {
  url = new Url;
  url.setModuleAction("dPadmissions", "print_sorties");
  url.addParam("date", "{{$date}}");
  url.popup(700, 550, "Sorties");
}

function loadTransfert(oForm){
  sejour_id   = $V(oForm.sejour_id)
  mode_sortie = $V(oForm.mode_sortie);
  // si Transfert, affichage du select
  if(mode_sortie=="transfert"){
    //Chargement de la liste des etablissement externes
    var url = new Url();
    url.setModuleAction("dPadmissions", "httpreq_vw_etab_externes");
    url.addParam("sejour_id", sejour_id);
    url.requestUpdate('listEtabExterne-'+oForm.name, { waitingText : null });
  } else {
    // sinon, on vide le contenu de la div
    $("listEtabExterne-" + oForm.name).innerHTML = "";
  }
}

function reload(mode) {
  var url = new Url;
  url.setModuleAction("dPadmissions", "httpreq_vw_sorties");
  url.addParam("date", "{{$date}}");
  url.addParam("vue", "{{$vue}}");
  url.addParam("mode", mode);
  url.requestUpdate('sorties'+mode, { waitingText : null });
}

function submitSortie(oForm, mode) {
  submitFormAjax(oForm, 'systemMsg', { onComplete : function() { reload(mode) } });
}

function confirmation(oForm, mode){
   if(!checkForm(oForm)){
     return false;
   }
   if(confirm('La date enregistrée de sortie est différente de la date prévue, souhaitez vous confimer la sortie du patient ?')){
     submitSortie(oForm, mode);
   }
}

function confirmation(date_actuelle, date_demain, sortie_prevue, entree_reelle, oForm, mode){
  if(entree_reelle == ""){
    if(!confirm('Attention, ce patient ne possède pas de date d\'entrée réelle, souhaitez vous confirmer la sortie du patient ?')){
     return false;
    }
  }
  if(date_actuelle > sortie_prevue || date_demain < sortie_prevue) {
    if(!confirm('La date enregistrée de sortie est différente de la date prévue, souhaitez vous confimer la sortie du patient ?')){
     return false;
    }
  }
  submitSortie(oForm, mode);    
}

Main.add(function () {
  var ambuUpdater = new Url;
  ambuUpdater.setModuleAction("dPadmissions", "httpreq_vw_sorties");
  ambuUpdater.addParam("date", "{{$date}}");
  ambuUpdater.addParam("vue", "{{$vue}}");
  ambuUpdater.addParam("mode", "ambu");
  ambuUpdater.periodicalUpdate('sortiesambu', { frequency: 90 });
  
  var compUpdater = new Url;
  compUpdater.setModuleAction("dPadmissions", "httpreq_vw_sorties");
  compUpdater.addParam("date", "{{$date}}");
  compUpdater.addParam("vue", "{{$vue}}");
  compUpdater.addParam("mode", "comp");
  compUpdater.periodicalUpdate('sortiescomp', { frequency: 90 });

  Calendar.regRedirectPopup("{{$date}}", "?m={{$m}}&tab={{$tab}}&date=");
});

</script>

<table class="main">
  <tr>
    <td class="halfPane">
      <form name="typeVue" action="?m={{$m}}" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <label for="vue" title="Choisir un type de vue">Type de vue</label>
      <select name="vue" onchange="submit()">
        <option value="0" {{if $vue == 0}}selected="selected"{{/if}}>Tout afficher</option>
        <option value="1" {{if $vue == 1}}selected="selected"{{/if}}>Ne pas afficher les sorties effectuées</option>
      </select>
      </form>
    </td>
    <td class="halfPane" style="text-align: center">
      <a style="float: right;" href="#" onclick="printPlanning()" class="buttonprint">Imprimer</a>
      <strong>
        <a href="?m=dPadmissions&amp;tab=vw_idx_sortie&amp;date={{$hier}}"> <<< </a>
        {{$date|date_format:"%A %d %B %Y"}}
        <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
        <a href="?m=dPadmissions&amp;tab=vw_idx_sortie&amp;date={{$demain}}"> >>> </a>
      </strong>
    </td>
  </tr>
  <tr>
    <td id="sortiesambu">
    </td>
    <td id="sortiescomp">
    </td>
  </tr>
</table>
