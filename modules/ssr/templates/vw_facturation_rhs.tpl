{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module="ssr" script="cotation_rhs"}}

<script type="text/javascript">
Sejour = {
  refresh: function(rhs_date_monday) {
    var url = new Url("ssr", "ajax_sejours_to_rhs_date_monday");
    url.addParam("rhs_date_monday", rhs_date_monday);
    url.requestUpdate("rhs-no-charge-"+rhs_date_monday);
  },
  
  search: function() {
	var form = getForm('rhs-search');
    var url = new Url("ssr", "ajax_sejours_rhs_search");
	url.addElement(form.nda);
	url.requestUpdate("rhs-search-result");
	return false;
  }
}
</script>

{{if !$rhs_counts}}
  <div class="small-info">{{tr}}CRHS-none{{/tr}}</div>
{{else}}
  <table class="main">
    <tr>
      <td class="narrow">
        <script type="text/javascript">
        Main.add(function() {
          Control.Tabs.create('tabs-rhss_no_charge', true).activeLink.up().onmousedown();
        });
        </script>
        <ul id="tabs-rhss_no_charge" class="control_tabs_vertical" style="width: 14em;">
          <li onmousedown="">
            <a href="#rhs-search" class="empty">{{tr}}Search{{/tr}} <small>(&ndash;)</small></a>
          </li>
          {{foreach from=$rhs_counts item=_rhs_count}}
          <li onmousedown="Sejour.refresh('{{$_rhs_count.mondate}}')">
            <a href="#rhs-no-charge-{{$_rhs_count.mondate}}">
              {{tr}}Week{{/tr}} {{$_rhs_count.mondate|date_format:"%U"}}
              <small class="count">({{$_rhs_count.count}})</small>
              <br />
              <small>
                du {{$_rhs_count.mondate|date_format:$conf.date}}
                <br />
                au {{$_rhs_count.sundate|date_format:$conf.date}}
              </small>
            </a>
          </li>
          {{/foreach}}
        </ul>
      </td>
      <td>
        <div id="rhs-search">
          <form name="rhs-search" action="?" method="get" onsubmit="return Sejour.search();">
          <table class="form">
            <tr>
              <th><label for="nda">Numero de dossier</label></th>
              <td><input name="nda" type="text" /></td>
              <td class="button"><button class="search" onsubmit>{{tr}}Search{{/tr}}</button></td>
            </tr>
          </table>
          </form>
          <div id="rhs-search-result">
          </div>
        </div>
        {{foreach from=$rhs_counts item=_rhs_count}}
        <div id="rhs-no-charge-{{$_rhs_count.mondate}}" style="display: none;">
        </div>
        {{/foreach}}
      </td>
    </tr>
  </table>
{{/if}}