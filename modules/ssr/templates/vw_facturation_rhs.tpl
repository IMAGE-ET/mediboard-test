{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module="ssr" script="cotation_rhs"}}

<script type="text/javascript">
  refreshSejour = function(rhs_date_monday) {
	  var url = new Url("ssr", "ajax_sejours_to_rhs_date_monday");
	  url.addParam("rhs_date_monday", rhs_date_monday);
	  url.requestUpdate("rhs-no-charge-"+rhs_date_monday);
  }
   
  Main.add(function(){
	  {{if $rhs_counts}}
	    Control.Tabs.create('tabs-rhss_no_charge', true).activeLink.up().onmousedown();
    {{/if}}
  });
</script>

{{if !$rhs_counts}}
  <div class="small-info">{{tr}}CRHS-none{{/tr}}</div>
{{else}}
  <table class="main">
    <tr>
      <td class="narrow">
        <ul id="tabs-rhss_no_charge" class="control_tabs_vertical" style="width: 14em;">
          {{foreach from=$rhs_counts item=_rhs_count}}
          <li onmousedown="refreshSejour('{{$_rhs_count.mondate}}')">
            <a href="#rhs-no-charge-{{$_rhs_count.mondate}}">
              {{tr}}Week{{/tr}} {{$_rhs_count.mondate|date_format:"%U"}}
							<small class="count">({{$_rhs_count.count}})</small>
              <br />
              <small>
                du {{$_rhs_count.mondate|date_format:$dPconfig.date}}
								<br />
                au {{$_rhs_count.sundate|date_format:$dPconfig.date}}
              </small>
            </a>
          </li>
          {{/foreach}}
        </ul>
      </td>
      <td>
        {{foreach from=$rhs_counts item=_rhs_count}}
        <div id="rhs-no-charge-{{$_rhs_count.mondate}}" style="display: none;">
        </div>
        {{/foreach}}
      </td>
    </tr>
  </table>
{{/if}}