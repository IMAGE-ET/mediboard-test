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
DependancesRHSGraphs = window.DependancesRHSGraphs || {};

DependancesRHSGraphs["{{$rhs->_id}}"] = function(){
  Flotr.draw("radar-dependances-{{$dependances->_guid}}", 
  [[
    [0, {{$dependances->habillage}}],
    [1, {{$dependances->deplacement}}],
    [2, {{$dependances->alimentation}}],
    [3, {{$dependances->continence}}],
    [4, {{$dependances->comportement}}],
    [5, {{$dependances->relation}}]
  ]],
  {
    radar: {show: true},
    grid: {circular: true, minorHorizontalLines: true},
    xaxis: {ticks:[
      [0, "{{tr}}CDependancesRHS-habillage-court{{/tr}}"],
      [1, "{{tr}}CDependancesRHS-deplacement-court{{/tr}}"],
      [2, "{{tr}}CDependancesRHS-alimentation-court{{/tr}}"],
      [3, "{{tr}}CDependancesRHS-continence-court{{/tr}}"],
      [4, "{{tr}}CDependancesRHS-comportement-court{{/tr}}"],
      [5, "{{tr}}CDependancesRHS-relation-court{{/tr}}"]
    ]},
    yaxis: {min: 0, max: 4}
  });
};

try {
  DependancesRHSGraphs["{{$rhs->_id}}"]();
} catch(e) {}

</script>

<div id="radar-dependances-{{$dependances->_guid}}" style="width: 300px; height: 300px; cursor: pointer;"
     onclick="CotationRHS.editDependancesRHS({{$rhs->_id}})" title="{{tr}}Edit{{/tr}}"></div>
