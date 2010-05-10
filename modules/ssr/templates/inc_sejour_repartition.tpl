{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<div class="ssr-sejour-bar" title="arrivée il y a {{$_sejour->_entree_relative}}j et départ prévu dans {{$_sejour->_sortie_relative}}j ">
	<div style="width: {{math equation='100*(-entree / (duree))' entree=$_sejour->_entree_relative duree=$_sejour->_duree format='%.2f'}}%;"></div>
</div>
  
{{assign var=patient value=$sejour->_ref_patient}}
<span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
  {{$patient}}        
</span> 

<div style="text-indent: 1em; opacity: 0.8;">
  {{mb_value object=$sejour field=libelle}}
</div>