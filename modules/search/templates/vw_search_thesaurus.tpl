{{*
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

{{mb_script module=search script=Search}}
{{mb_script module=search script=Thesaurus ajax=true}}
<script>
  Main.add(function () {
    var form = getForm("esSearchFavoris");
    window.calendar_min_date = Calendar.regField(form._min_date);
    window.calendar_max_date = Calendar.regField(form._max_date);
    window.calendar_date = Calendar.regField(form._date);
    form.onsubmit();
    Thesaurus.updateListThesaurus($V(form.elements.start_thesaurus));
  });

  function changePage(start) {
    var form = getForm("esSearchFavoris");
    $V(form.elements.start, start);
    form.onsubmit();
  }
  function changePageThesaurus(start) {
    var form = getForm("esSearchFavoris");
    $V(form.elements.start_thesaurus, start);
    Thesaurus.updateListThesaurus(start);
  }

</script>
<form method="get" name="esSearchFavoris" action="?m=search" class="watched prepared" onsubmit="return Thesaurus.displayResultsThesaurus(this);" onchange="onchange=$V(this.form.start, '0'); $V(this.form.start_thesaurus, '0')">
  <input type="hidden" name="start" value="0">
  <input type="hidden" name="start_thesaurus" value="0">
  <input type="hidden" name="user_id" value="{{$app->_ref_user->_id}}">
  <table class="main layout">
    <tr>
      <!-- Fieldset de tri par date -->
      <td style="width: 30%">
        {{mb_include module=search template=inc_header_filters_search display_user=false display_types=false query=false expand=false}}
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
                  <label><input type="checkbox" name="user_id" value="" checked></label>
                </span>
                <span class="circled">
                  <img src="images/icons/user-function.png" title="Favoris pour la fonction utilisateur">
                  <label><input type="checkbox" name="function_id" value="" checked></label>
                </span>
                <span class="circled">
                      <img src="images/icons/group.png" title="Favoris pour l'établissement">
                       <label><input type="checkbox" name="group_id" value="" checked></label>
                </span>
              </td>
              <td>
                {{foreach from=$contextes item=_contexte}}
                   <span class="circled">
                      <label for="{{$_contexte}}"> {{$_contexte}}</label>
                      <input type="checkbox" name="contextes[]" id="{{$_contexte}}" value="{{$_contexte}}" onclick="" checked>
                   </span>
                {{/foreach}}
              </td>
            </tr>
          </table>
        </fieldset>
        <div id="list_thesaurus_entry">
        </div>
      </td>
    </tr>
  </table>
</form>


