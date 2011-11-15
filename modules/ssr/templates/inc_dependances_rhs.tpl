{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
  * @package Mediboard
  * @subpackage ssr
  * @version $Revision: 7951 $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}
  
{{assign var=dependances value=$rhs->_ref_dependances}}

<table class="form">
  <tr>
    <th class="title">
      {{tr}}CDependancesRHS{{/tr}}
    </th>
  </tr>
</table>

<script>
CotationRHS.drawDependancesGraph(
  $("radar-dependances-{{$dependances->_guid}}"), 
	"{{$rhs->_id}}", 
	[
	  {{foreach from=$rhs->_ref_dependances_chonology item=_dep key=_date name=_deps}}
	    {
			  label: "S{{$_date}}",
				{{if $_date != "+0"}} 
          radar: {
						fillOpacity: 0.1,
	          lineWidth: 0.5
					},
				{{/if}}
				data: [
					[0, {{$_dep->habillage}}],
			    [1, {{$_dep->deplacement}}],
			    [2, {{$_dep->alimentation}}],
			    [3, {{$_dep->continence}}],
			    [4, {{$_dep->comportement}}],
			    [5, {{$_dep->relation}}]
				]
			}{{if !$smarty.foreach._deps.last}},{{/if}}
		{{/foreach}}
  ]
);
</script>

<div id="radar-dependances-{{$dependances->_guid}}" style="width: 250px; height: 250px; cursor: pointer;"
     onclick="CotationRHS.editDependancesRHS({{$rhs->_id}})" title="{{tr}}Edit{{/tr}}"></div>
