{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=bloc script=edit_planning}}

<script>
  togglePlayPause = function(button) {
    button.toggleClassName("play");
    button.toggleClassName("pause");
    if (!(window.autoRefreshSuivi)) {
      window.autoRefreshSuivi = setInterval(function(){
        updateSuiviSalle();
      }, ({{math equation="a*1000" a=$conf.dPbloc.CPlageOp.time_autorefresh}}));
    }
    else {
      clearTimeout(window.autoRefreshSuivi);
    }
  };

  updateSuiviSalle = function() {
    var oform   = getForm('changeDate');
    var date    = $V(oform.date);
    var bloc_id = $V(oform.bloc_id);

    if (bloc_id) {
      var url = new Url("dPbloc", "ajax_vw_suivi_salle");
      url.addParam('bloc_id', bloc_id);
      url.addParam('date', date);
      var str = DateFormat.format(Date.fromDATE(date), " dd/MM/yyyy");
      if (date == '{{$date}}') {
        str= str+" (Aujourd'hui)";
      }
      $('dateSuiviSalle').update(str);
      url.requestUpdate("result_suivi");
    }
  };

  printFicheBloc = function(interv_id) {
  var url = new Url("dPsalleOp", "print_feuille_bloc");
  url.addParam("operation_id", interv_id);
  url.popup(700, 600, 'FeuilleBloc');
  };

  printAnapath = function() {
    var form = getForm('changeDate');
    var url = new Url("dPbloc", "print_anapath");
    url.addParam("date"   , $V(form.date));
    url.addParam("bloc_id", $V(form.bloc_id));
    url.popup(700, 600, 'Anapath');
  };

  printBacterio = function() {
    var form = getForm('changeDate');
    var url = new Url("dPbloc", "print_bacterio");
    url.addParam("date"   , $V(form.date));
    url.addParam("bloc_id", $V(form.bloc_id));
    url.popup(700, 600, 'Bacterio');
  };

  showLegend = function() {
    var url = new Url('bloc', 'legende').requestModal()
  };

  Main.add(function () {
    {{if $blocs|@count}}
      Calendar.regField(getForm("changeDate").date, null, {noView: true});
      updateSuiviSalle();
      if (Preferences.startAutoRefreshAtStartup == 1) {
        togglePlayPause($('autorefreshSuiviSalleButton'));
      }
    {{/if}}
  });
</script>

{{if $blocs|@count}}
  <table class="main not-printable">
    <tr>
      <td>
        <button id="autorefreshSuiviSalleButton" style="float: left;" class="play" title="Rechargement automatique de la page ({{tr}}config-dPbloc-CPlageOp-time_autorefresh-{{$conf.dPbloc.CPlageOp.time_autorefresh}}{{/tr}})" onclick="togglePlayPause(this);">Rech. Auto</button>
        <button type="button" onclick="showLegend()" class="search" style="float: right;">Légende</button>
        <button type="button" onclick="$('suivi-salles').print();" class="print" style="float: right;">{{tr}}Print{{/tr}}</button>
        <button type="button" onclick="printAnapath();" class="print" style="float: right;">{{tr}}COperation-anapath{{/tr}}</button>
        <button type="button" onclick="printBacterio();" class="print" style="float: right;">{{tr}}COperation-labo{{/tr}}</button>

        <form action="?" name="changeDate" method="get">
          <label> Bloc :
            <select name="bloc_id" onchange="updateSuiviSalle();">
              {{foreach from=$blocs item=curr_bloc}}
                <option value="{{$curr_bloc->_id}}">
                  {{$curr_bloc->nom}}
                </option>
              {{/foreach}}
            </select>
          </label>
          <label>Date :
            <input type="hidden" name="date" class="date" value="{{$date}}" onchange="updateSuiviSalle();" /><span id="dateSuiviSalle"></span>
          </label>
        </form>
      </td>
    </tr>
  </table>
{{/if}}

<div id="result_suivi">
  {{if !$blocs|@count}}
    <div class="small-warning">
      {{tr}}dPbloc-msg-no_bloc{{/tr}}
    </div>
  {{/if}}
</div>