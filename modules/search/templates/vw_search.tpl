{{*
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @link     http://www.mediboard.org*}}

{{mb_script module="search" script="Search" ajax=true}}
{{mb_script module="search" script="Thesaurus" ajax=true}}
<script>
  Main.add(function () {
    var form = getForm("esSearch");
    Calendar.regField(form._min_date);
    Calendar.regField(form._max_date);
    Calendar.regField(form._date);
    Search.getAutocompleteUser(form);
    Thesaurus.getAutocompleteFavoris(form);
  });

  function changePage(start) {
    var form = getForm("esSearch");
    $V(form.elements.start, start);
    form.onsubmit();
  }
</script>

<form method="get" name="esSearch" action="?m=search" class="watched prepared" onsubmit="return Search.displayResults(this);" onchange="onchange=$V(this.form, '0')">
  <input type="hidden" name="start" value="0">
  <input type="hidden" name="accept_utf8" value="1">
  <input type="hidden" name="contexte" value="classique">
  <div>
    <!-- Barre de recherche -->
    {{mb_include module=search template=inc_header_search}}
  </div>
  <div>
    <!-- Filtres de recherche -->
    {{mb_include module=search template=inc_header_filters_search}}
  </div>
  <div id="list_result">
    <!-- Résultats de la Recherche -->
  </div>
</form>