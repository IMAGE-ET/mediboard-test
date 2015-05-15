{{*
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<!--Vue appellée dans la recherche automatique, c'est le volet recherche classique.-->

{{mb_script module="search" script="Search" ajax=true}}
{{mb_script module="search" script="Thesaurus" ajax=true}}
<script>
  Main.add(function () {
    var form = getForm("esSearch");
    Calendar.regField(form._min_date);
    Calendar.regField(form._max_date);
    Calendar.regField(form._date);
    Thesaurus.getAutocompleteFavoris(form);
    Search.getAutocompleteUser(form, '{{$contexte}}');
  });

  changePage = function (start) {
    var form = getForm("esSearch");
    $V(form.elements.start, start);
    form.onsubmit();
  };
</script>

<form method="get" name="esSearch" action="?m=search&tab=vw_search_auto" class="watched prepared"
      onsubmit="return Search.displayResults(this);" onchange="onchange=$V(this.form, '0')">
  <input type="hidden" name="start" value="0"/>
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="accept_utf8" value="1"/>
  <input type="hidden" name="contexte" value="{{$contexte}}"/>

  <div id="search_bar">
    <!-- Barre de recherche -->
    {{mb_include module=search template=inc_header_search aggreg=false fuzzy=false tooltip_help=false}}
  </div>
  <div id="search_bar">
    <!-- Filtres de recherche -->
    {{mb_include module=search template=inc_header_filters_search}}
  </div>
  <div id="list_result">
    <!-- Résultats de la Recherche -->
  </div>
</form>