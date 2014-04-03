{{*
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @link     http://www.mediboard.org*}}

{{mb_script module="search" script="Search" ajax=true}}
<script>
  Main.add(function () {
    var form = getForm("esSearch");
    Search.selectPraticien(form.specificUser_id, form.specificUser_view);
  });

  function changePage(start) {
    var form = getForm("esSearch");
    $V(form.elements.start, start);
    form.onsubmit();
  }
</script>

<form method="get" name="esSearch" action="?m=search" class="watched prepared" onsubmit="return Search.displayResults(this);" onchange="onchange=$V(this.form, '0')">
  <input type="hidden" name="start" value="0">
  <table class="main">
    <tbody>
    <tr>
      <td id="td_container_search" style="width:70%;">
        <fieldset>
          <legend>Recherche</legend>
          <table>
            <tbody>
              <tr id="tr_search_bar">
                <th id="th_search_bar">Barre de Recherche</th>
                <td>
                  <input type="search" id="words" name="words" value="" placeholder="Saisissez les termes de votre recherche ici..." style="width:700px;" onchange="$V(this.form.start, '0')" >
                </td>
              </tr>
              <tr>
                <th>Date</th>
                <td>
                  <input type="radio" name="date_interval" id="dateBetween" value="between" onclick="$('span_date').show(); $('date_fin').show()">
                  <label>Comprise Entre</label>
                  <input type="radio" name="date_interval" id="dateSince" value="since" onclick="$('span_date').hide(); $('date_fin').hide()">
                  <label>Depuis</label>
                  <input type="radio" name="date_interval" id="dateUniqueDay" value="uniqueDay" checked="checked" onclick="$('span_date').hide(); $('date_fin').hide()">
                  <label>Jour seul</label>
                </td>
              </tr>
              <tr>
                <th></th>
                <td>
                  <label>Début</label>
                  <input type="date" id="date_deb" name="date_deb" onchange="$V(this.form.start, '0')" >
                  <span id="span_date" style="display: none;">et fin</span>
                  <input type="date" id="date_fin" name="date_fin" style="display: none;" onchange="$V(this.form.start, '0')" >
                </td>
              </tr>
              <tr>
                <th>Filtre utilisateurs</th>
                <td>
                  <input type="hidden" name="specificUser_id" value=""/>
                  <input type="text" name="specificUser_view" class="autocomplete" style="width:15em;" placeholder="&mdash; Choisir un praticien"
                         value="" />
                  <input name="_limit_search_sejour" class="changePrefListUsers" type="checkbox"
                         {{if $app->user_prefs.useEditAutocompleteUsers}}checked{{/if}}
                         title="Limiter la recherche des praticiens" onchange="$V(this.form.start, '0')" />

                  <button type="button" class="user notext"
                          onclick="$V(this.form.elements.specificUser_id, '{{$app->user_id}}');
                            $V(this.form.specificUser_view, '{{$app->_ref_user}}');">
                  </button>
                  <button type="button" class="erase notext"
                          onclick="$V(this.form.elements.specificUser_id, '');
                          $V(this.form.specificUser_view, '');">
                  </button>
                </td>
              </tr>
              <tr>
                <td>
                  <button type="submit" id="button_search" class="button lookup">Démarrer la recherche</button>
                </td>
                <td style="float: right;">
                  <a href="#" id="a_advanced_search" class="button down" onclick="Search.toggleElement($('advanced_search')); Search.toggleElement($('a_advanced_search'))"> Recherche avancée</a>
                </td>

              </tr>
            </tbody>
          </table>
          <table id="advanced_search" style="display:none;">
            <tbody>
                <tr>
                  <th id="th_selection_where">Sélectionner où rechercher : </th>
                  <td>
                    <a class="button" href="#" id="field_titre" onclick="Search.assignFieldText($('words'), 'title:')">Titre du document</a>
                    <a class="button" href="#" id="field_body" onclick="Search.assignFieldText($('words'), 'body:')">Corps du document</a>
                  </td>
                </tr>
                <tr>
                  <th id="th_selection_op">Sélectionner un opérateur (par défaut l'opérateur est ET) :</th>
                  <td>
                    <a class="button add" href="#" id="button_add" onclick="Search.assignFieldText($('words'), '+')">Mot obligatoire (exemple : +Douleur)</a>
                    <a class="button" href="#" title="&&" id="button_and" onclick="Search.assignFieldText($('words'), ' && ')">ET </a>
                    <a class="button" href="#" title="||" id="button_or" onclick="Search.assignFieldText($('words'), ' || ')">OU </a>
                    <a class="button" href="#" title="!" id="button_not" onclick="Search.assignFieldText($('words'), ' ! ')">PAS</a>
                    <a class="button remove" href="#" id="button_remove" onclick="Search.assignFieldText($('words'), '-')"> Mot interdit (exemple : -Hanche)</a>
                    <a class="button" href="#" title="~ , je recherche ce mot avec erreurs de frappes, etc..." id="button_environ" onclick="Search.assignFieldText($('words'), '~')"> Environ (exemple : ~Douleur)</a>
                    <a class="button" href="#" id="button_compose" title="*, un mot commençant/finissant par..." onclick="Search.assignFieldText($('words'), '*')">Composé de (exemple : *leur, Dou*, do*eur)</a>
                    <a class="button" href="#" id="button_container" title="?, un mot contenant un caractère inconnu" onclick="Search.assignFieldText($('words'), '?')">Contenant (exemple : Do?leur, ?anche)</a>
                  </td>
                </tr>
                <tr>
                  <td>
                    <a href="#" class="button new" id="button_examples" onclick="Search.popupExample()"> Cliquez ici pour obtenir des exemples</a>
                  </td>
                </tr>
            </tbody>
          </table>
        </fieldset>
      </td>
    </tr>
    <tr>
      <td>
        <div id="list_result">
          <!-- Résultats de la Recherche par des tr -->
        </div>
      </td>
    </tr>
    </tbody>
  </table>
</form>




