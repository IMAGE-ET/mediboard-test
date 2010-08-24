{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
  * @package Mediboard
  * @subpackage ssr
  * @version $Revision: 7951 $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *
}}
  
{{assign var=dependances value=$rhs->_ref_dependances}}
<table class="form">
  <tr>
    <th class="title" colspan="2">{{tr}}CDependancesRHS{{/tr}}</th>
  </tr>
  <tr>
    <th class="category">Catégorie</th>
    <th class="category">Degré</th>
  </tr>
  <tr>
    <th>{{mb_label object=$dependances field=habillage}}</th>
    <td>{{mb_value object=$dependances field=habillage}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$dependances field=deplacement}}</th>
    <td>{{mb_value object=$dependances field=deplacement}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$dependances field=alimentation}}</th>
    <td>{{mb_value object=$dependances field=alimentation}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$dependances field=continence}}</th>
    <td>{{mb_value object=$dependances field=continence}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$dependances field=comportement}}</th>
    <td>{{mb_value object=$dependances field=comportement}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$dependances field=relation}}</th>
    <td>{{mb_value object=$dependances field=relation}}</td>
  </tr>
</table>