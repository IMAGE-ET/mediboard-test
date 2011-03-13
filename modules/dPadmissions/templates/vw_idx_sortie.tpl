{{* $Id: *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=dPadmissions script=admissions}}

<script type="text/javascript">

function printAmbu(){
  var url = new Url("dPadmissions", "print_ambu");
  url.addParam("date", "{{$date}}");
  url.popup(800,600,"Ambu");
}

function printPlanning(type_sejour) {
  var url = new Url("dPadmissions", "print_sorties");
  url.addParam("date", "{{$date}}");
  url.addParam("type", type_sejour);
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

var changeEtablissementId = function(oForm) {
  $V(oForm._modifier_sortie, '0');
  var type = $V(oForm.type);
  submitSortie(oForm, type);
}

function reload(type_sejour) {
  var url = new Url("dPadmissions", "httpreq_vw_sorties");
  url.addParam("date", "{{$date}}");
  url.addParam("vue", "{{$vue}}");
  url.addParam("filter_function_id" , "{{$filter_function_id}}");
  url.addParam("type_sejour", type_sejour);
  url.requestUpdate("listSorties");
}

function submitSortie(oForm, type_sejour) {
  submitFormAjax(oForm, 'systemMsg', { onComplete : function() { reload(type_sejour) } });
}

function confirmation(oForm, type_sejour){
   if(!checkForm(oForm)){
     return false;
   }
   if(confirm('La date enregistrée de sortie est différente de la date prévue, souhaitez vous confimer la sortie du patient ?')){
     submitSortie(oForm, type_sejour);
   }
}

function confirmation(date_actuelle, date_demain, sortie_prevue, entree_reelle, oForm, type_sejour){
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
  submitSortie(oForm, type_sejour);    
}

Main.add(function () {
  var totalUpdater = new Url("dPadmissions", "httpreq_vw_all_sorties");
  totalUpdater.addParam("date", "{{$date}}");
  totalUpdater.periodicalUpdate('allSorties', { frequency: 120 });
  
  var listUpdater = new Url("dPadmissions", "httpreq_vw_sorties");
  listUpdater.addParam("type_sejour", "{{$type_sejour}}");
  listUpdater.addParam("date", "{{$date}}");
  listUpdater.periodicalUpdate('listSorties', { frequency: 120 });

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

        <select name="filter_function_id" style="width: 16em;" onchange="this.form.submit();">
          <option value=""> &mdash; Toutes les fonctions</option>
          {{foreach from=$functions item=_function}}
            <option value="{{$_function->_id}}" {{if $_function->_id == $filter_function_id}}selected="selected"{{/if}} class="mediuser" style="border-color: #{{$_function->color}};">{{$_function}}</option>
          {{/foreach}}
        </select>

      </form>
    </td>
    <td class="halfPane" style="text-align: center">
      <strong>
        <a href="?m=dPadmissions&amp;tab=vw_idx_sortie&amp;date={{$hier}}"> <<< </a>
        {{$date|date_format:$conf.longdate}}
        <form name="changeDate" action="?m={{$m}}" method="get">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="tab" value="vw_idx_sortie" />
          <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
        </form>
        <a href="?m=dPadmissions&amp;tab=vw_idx_sortie&amp;date={{$demain}}"> >>> </a>
      </strong>
    </td>
  </tr>
</table>

<table class="main">
  <tr>
    <td id="allSorties" style="width: 250px">
    </td>
    <td id="listSorties" style="width: 100%">
    </td>
  </tr>
</table>