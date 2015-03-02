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

  changePage = function(start) {
    var form = getForm("esSearchFavoris");
    $V(form.elements.start, start);
    form.onsubmit();
  };

  changePageThesaurus = function (start) {
    var form = getForm("esFilterFavoris");
    $V(form.elements.start_thesaurus, start);
    Thesaurus.updateListThesaurus(start);
  }

</script>

  <table class="main layout">
    <tr>
      <!-- Fieldset de tri par date -->
      <td style="width: 30%">
        <form method="get" name="esSearchFavoris" action="?m=search" class="watched prepared" onsubmit="return Thesaurus.displayResultsThesaurus(this);" onchange="$V(this.form.start, '0'); $V(this.form.start_thesaurus, '0')">
          <input type="hidden" name="start" value="0">
          <input type="hidden" name="user_id" value="{{$app->_ref_user->_id}}">
          {{mb_include module=search template=inc_header_filters_search display_user=false display_types=false query=false expand=false submit=true}}
          <div id="list_log_result">
            {{mb_include module=search template=inc_search_result_thesaurus}}
          </div>
        </form>
      </td>
      <td id="list_favoris">
        <fieldset>
          <legend>Filtres</legend>
          <form method="get" name="esFilterFavoris" action="?m=search" class="watched prepared" onsubmit="return Thesaurus.filterListThesaurus(this);" onchange="$V(this.form.start_thesaurus, '0')">
            <input type="hidden" name="start_thesaurus" value="0">
            <table>
            <tr>
              <td class="halfPane">
                  <span class="circled">
                    <img src="images/icons/user.png">
                    <label><input type="radio" name="_choose" value="user_id {{$app->_ref_user->_id}}" onclick="this.form.onsubmit();" onchange="$V(this.form.start_thesaurus, '0')" checked></label>
                  </span>

                  <span class="circled">
                     <img src="images/icons/user-function.png">
                      <label><input id="function" type="radio" name="_choose" value="function_id {{$user->_ref_function->_id}}" onclick="this.form.onsubmit();" onchange="$V(this.form.start_thesaurus, '0')"></label>
                  </span>

                  <span class="circled">
                    <img src="images/icons/group.png">
                    <label><input id="group" type="radio" name="_choose" value="group_id {{$g}}" onclick="this.form.onsubmit();" onchange="$V(this.form.start_thesaurus, '0')"></label>
                  </span>
              </td>
              <td>
                {{foreach from=$contextes item=_contexte}}
                   <span class="circled">
                      <label for="{{$_contexte}}"> {{$_contexte}}</label>
                      <input type="checkbox" name="contextes[]" id="{{$_contexte}}" value="{{$_contexte}}" onclick="this.form.onsubmit();" onchange="$V(this.form.start_thesaurus, '0')">
                   </span>
                {{/foreach}}
              </td>
            </tr>
          </table>
          </form>
        </fieldset>
        <div id="list_thesaurus_entry">
        </div>
      </td>
    </tr>
  </table>


