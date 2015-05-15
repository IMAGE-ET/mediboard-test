{{*
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<!--Vue générale de recherche dans le journal de recherche. onglet du même nom -->

{{mb_script module="search" script="Search" ajax=true}}
<script>
  Main.add(function () {
    var form = getForm("esLogSearch");
    Calendar.regField(form._min_date);
    Calendar.regField(form._max_date);
    Calendar.regField(form._date);
    Search.getAutocompleteUser(form, "log");
    window.user_tag_token.add('{{$app->user_id}}');
    insertTag('{{$app->_ref_user->_guid}}', '{{$app->_ref_user}}');
  });

  function changePage(start) {
    var form = getForm("esLogSearch");
    $V(form.elements.start, start);
    form.onsubmit();
  }

  function insertTag(guid, name) {
    var tag = $("CTag-" + guid);

    if (!tag) {
      var btn = DOM.button({
        "type": "button",
        "className": "delete",
        "style": "display: inline-block !important",
        "onclick": "window.user_tag_token.remove($(this).up('li').get('user_id')); this.up().remove();"
      });

      var li = DOM.li({
        "data-tag_item_id": guid,
        "id": "CTag-"+guid,
        "className": "tag"
      }, name, btn);

      $("user_tags").insert(li);
    }
  }

</script>

<form method="get" name="esLogSearch" action="?m=search" class="watched prepared" onsubmit="return Search.displayLogResults(this);" onchange="onchange=$V(this.form, '0')">
  <input type="hidden" name="start" value="0">
  <input type="hidden" name="accept_utf8" value="1">

  <div id="search_bar">
    <!-- Barre de recherche -->
    {{mb_include module=search template=inc_header_search}}
  </div>

  <div id="search_filter">
    <!-- Filtres de recherche -->
    {{mb_include module=search template=inc_header_filters_search display_contextes=true}}
  </div>

  <div id="list_log_result">
    <!-- Résultats de la Recherche -->
  </div>
</form>