{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

function printAmbu(date){
  var url = new Url("dPadmissions", "print_ambu");
  url.addParam("date", "{{$date}}");
  url.popup(800,600,"Ambu");
}

function printPlanning(type) {
  var url = new Url("dPadmissions", "print_sorties");
  url.addParam("date", "{{$date}}");
  url.addParam("type", type);
  url.popup(700, 550, "Sorties");
}

function loadTransfert(oForm){
  sejour_id   = $V(oForm.sejour_id)
  mode_sortie = $V(oForm.mode_sortie);
  // si Transfert, affichage du select
  if(mode_sortie=="transfert"){
    //Chargement de la liste des etablissement externes
    var url = new Url("dPadmissions", "httpreq_vw_etab_externes");
    url.addParam("sejour_id", sejour_id);
    url.requestUpdate('listEtabExterne-'+oForm.name);
  } else {
    // sinon, on vide le contenu de la div
    $("listEtabExterne-" + oForm.name).innerHTML = "";
  }
}

changeEtablissementId = function(oForm) {
  oForm._modifier_sortie.value = '0';
  type = $V(oForm.type);
  if(type != 'ambu' && type != 'comp') {
    type = 'autre';
  }
  submitSortie(oForm, type);
}

function reload(mode) {
  var url = new Url("dPadmissions", "httpreq_vw_sorties");
  url.addParam("date", "{{$date}}");
  url.addParam("vue", "{{$vue}}");
  url.addParam("mode", mode);
  url.requestUpdate('sorties'+mode);
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
  var ambuUpdater = new Url("dPadmissions", "httpreq_vw_sorties");
  ambuUpdater.addParam("date", "{{$date}}");
  ambuUpdater.addParam("vue" , "{{$vue}}");
  ambuUpdater.addParam("mode", "ambu");
  ambuUpdater.periodicalUpdate('sortiesambu', { frequency: 90 });
  
  var compUpdater = new Url("dPadmissions", "httpreq_vw_sorties");
  compUpdater.addParam("date", "{{$date}}");
  compUpdater.addParam("vue" , "{{$vue}}");
  compUpdater.addParam("mode", "comp");
  compUpdater.periodicalUpdate('sortiescomp', { frequency: 90 });
  
  var compUpdater = new Url("dPadmissions", "httpreq_vw_sorties");
  compUpdater.addParam("date", "{{$date}}");
  compUpdater.addParam("vue" , "{{$vue}}");
  compUpdater.addParam("mode", "autre");
  compUpdater.periodicalUpdate('sortiesautre', { frequency: 90 });
  
  Control.Tabs.create("main_tab_group", true);

  Calendar.regField(getForm("changeDate").date, null, {noView: true});
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
      <strong>
        <a href="?m=dPadmissions&amp;tab=vw_idx_sortie&amp;date={{$hier}}"> <<< </a>
        {{$date|date_format:$dPconfig.longdate}}
        <form name="changeDate" action="?m={{$m}}" method="get">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="tab" value="vw_idx_sortie" />
          <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
        </form>
        <a href="?m=dPadmissions&amp;tab=vw_idx_sortie&amp;date={{$demain}}"> >>> </a>
      </strong>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <!-- Tabulations -->
        <ul id="main_tab_group" class="control_tabs">
          <li>
            <a href="#comp_ambu">
              Ambu + Hospi complète
              <button onclick="printPlanning('ambu_comp')" class="notext print">{{tr}}Print{{/tr}}</button>
            </a>
          </li>
          <li>
            <a href="#autre">
              Autres
              <button onclick="printPlanning('autre')" class="notext print">{{tr}}Print{{/tr}}</button>
            </a>
          </li>
        </ul>
  
       <hr class="control_tabs" />
       <div id="comp_ambu" style="display:none">
         <table class="main">
           <tr>
             <td class="halfPane">
               <div id="sortiesambu"></div>
             </td>
             <td class="halfPane">
               <div id="sortiescomp"></div>
             </td>
           </tr>
         </table>
       </div>
       <div id="autre" style="display:none">
         <div id="sortiesautre"></div>
       </div>
    </td>
  </tr>
</table>