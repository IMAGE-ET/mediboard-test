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
	  {{if $rhss_no_charge}}
	    Control.Tabs.create('tabs-rhss_no_charge', true).activeLink.up().onmousedown();
    {{/if}}
  });
</script>

{{if !$rhss_no_charge}}
  <div class="small-info">{{tr}}no-rhs-to-charge{{/tr}}</div>
{{else}}
  <table class="main">
    <tr>
      <td style="width: 0.1%">
        <ul id="tabs-rhss_no_charge" class="control_tabs_vertical" style="width: 14em;">
          {{foreach from=$rhss_no_charge key=rhs_date_monday item=_rhss}}
            {{foreach from=$_rhss item=_rhs name=rhs}}
              {{if $smarty.foreach.rhs.first}}
              <li onmousedown="refreshSejour('{{$rhs_date_monday}}')">
                <a href="#rhs-no-charge-{{$rhs_date_monday}}">
                  {{$_rhs}} <span class="count">({{$_rhss|@count}})</span>
                  <br />
                  <small>
                    du {{mb_value object=$_rhs field=date_monday}}
                    au {{mb_value object=$_rhs field=_date_sunday}}
                  </small>
                </a>
              </li>
              {{/if}}
            {{/foreach}}
          {{/foreach}}
        </ul>
      </td>
      <td>
        {{foreach from=$rhss_no_charge key=rhs_date_monday item=_rhss}}
        <div id="rhs-no-charge-{{$rhs_date_monday}}" style="display: none;">
  
        </div>
        {{/foreach}}
      </td>
    </tr>
  </table>
{{/if}}