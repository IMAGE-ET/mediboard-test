{{*
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

{{mb_script module="search" script="Search" ajax=true}}
<script>
  changePage = function(start) {
    var form = getForm("esSearch");
    $V(form.elements.start, start);
    form.onsubmit();
  };
</script>
<form method="get" name="esSearch" action="?m=search&tab=vw_search_auto" class="watched prepared" onsubmit="return Search.displayResultsManual(this);" onchange="onchange=$V(this.form, '0')">
  <input type="hidden" name="start" value="0">
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}"/>
  <input type="hidden" name="contexte" value="pharmacie">
  <table class="main layout">
    <tbody>
    <tr>
      <td id="td_container_search">
        <input type="search" id="words" name="words" value="" placeholder="Saisissez les termes de votre recherche ici..." style="width:100%; height:1.5em; font-size:1em;" onchange="$V(this.form.start, '0')" autofocus>
      </td>
    </tr>
    </tbody>
  </table>
  <table class="main layout">
    <tbody>
    <tr>
      <td class="button" colspan="3">
        <button type="submit" id="button_search" class="button lookup">Démarrer la recherche</button>
      </td>
    </tr>
    </tbody>
  </table>
  <div id="list_result">
    <!-- Résultats de la Recherche -->
  </div>
</form>