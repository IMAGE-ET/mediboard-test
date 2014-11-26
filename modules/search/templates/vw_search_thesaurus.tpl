{{*
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

{{mb_script module=search script=Search}}
<script>
  Main.add(function () {
    var form = getForm("esSearchFavoris");
    window.calendar_date = Calendar.regField(form._min_date);
    window.calendar_date = Calendar.regField(form._max_date);
    window.calendar_date = Calendar.regField(form._date);
    form.onsubmit();
    filterContextes();
  });

  function changePage(start) {
    var form = getForm("esSearchFavoris");
    $V(form.elements.start, start);
    form.onsubmit();
  }

</script>
<form method="get" name="esSearchFavoris" action="?m=search" class="watched prepared" onsubmit="return Search.displayResultsThesaurus(this);" onchange="onchange=$V(this.form, '0')">
  <input type="hidden" name="start" value="0">
  <input type="hidden" name="user_id" value="{{$app->_ref_user->_id}}">
  <table class="main layout">
    <tr>
      <!-- Fieldset de tri par date -->
      <td class="halfPane">
        <fieldset>
          <legend>Intervalle de date </legend>
          <table>
            <tr>
              <td>
                <input type="hidden" class="datetime" id="_min_date" name="_min_date" onchange="$V(this.form.start, '0')" value="{{$date}}">
                <b>&raquo;</b>
                <input type="hidden" class="datetime" id="_max_date" name="_max_date" onchange="$V(this.form.start, '0')" >
              </td>
              <td>
                <strong>{{tr}}or{{/tr}}</strong>
                Jour seul : <input type="hidden" class="datetime" id="_date" name="_date" onchange="$V(this.form.start, '0')" >
              </td>
            </tr>
            <tr>
              <td class="button" colspan="2">
                <button type="submit" class="search">{{tr}}Search{{/tr}}</button>
              </td>
            </tr>
          </table>
        </fieldset>
        <div id="list_log_result">
          {{mb_include module=search template=inc_search_result_thesaurus}}
        </div>
      </td>
      <td id="list_favoris" class="halfPane">
        <fieldset>
          <legend>Filtres</legend>
          <table>
            <tr>
              <td class="halfPane">
                <span class="circled">
                  <img src="images/icons/user.png" title="Favoris pour l'utilisateur">
                  <input type="checkbox" name="user_id" value="" checked>
                </span>
                <span class="circled">
                  <img src="images/icons/user-function.png" title="Favoris pour la fonction utilisateur">
                  <input type="checkbox" name="function_id" value="" checked>
                </span>
                <span class="circled">
                      <img src="images/icons/group.png" title="Favoris pour l'établissement">
                      <input type="checkbox" name="group_id" value="" checked>
                </span>
              </td>
              <td>
                {{foreach from=$contextes item=_contexte}}
                   <span class="circled">
                      <label for="{{$_contexte}}"> {{$_contexte}}</label>
                      <input type="checkbox" name="contextes[]" id="{{$_contexte}}" value="{{$_contexte}}" onclick="filterContextes()" checked>
                   </span>
                {{/foreach}}
              </td>
            </tr>
          </table>
        </fieldset>
        {{mb_include module=search template=inc_search_thesaurus_entry}}
      </td>
    </tr>
  </table>
</form>


