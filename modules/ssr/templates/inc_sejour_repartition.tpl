{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !$remplacement}}
<div class="ssr-sejour-bar" title="arrivée il y a {{$sejour->_entree_relative}}j et départ prévu dans {{$sejour->_sortie_relative}}j ">
	<div style="width: {{math equation='100*(-entree / (duree))' entree=$sejour->_entree_relative duree=$sejour->_duree format='%.2f'}}%;"></div>
</div>
{{/if}}
  
{{assign var=patient value=$sejour->_ref_patient}}
<span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
  {{$patient->nom}} {{$patient->prenom}}
</span> 

<div class="motif">
	<div style="float: right;">
	  ({{$patient->_age}})
	</div>
  {{mb_value object=$sejour field=libelle}}
</div>

