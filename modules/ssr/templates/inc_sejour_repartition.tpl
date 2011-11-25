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
	<div style="width: {{if $sejour->_duree}}{{math equation='100*(-entree / (duree))' entree=$sejour->_entree_relative duree=$sejour->_duree format='%.2f'}}{{else}}100{{/if}}%;"></div>
</div>
{{/if}}
  
{{assign var=bilan value=$sejour->_ref_bilan_ssr}}
{{assign var=patient value=$sejour->_ref_patient}}
<span {{if $bilan->_encours}} class="encours" {{/if}} onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
  {{$patient->nom}} {{$patient->prenom}}
</span> 

<div class="libelle">
	<div style="float: right;">
	  ({{$patient->_age}})
	</div>

  {{if $bilan->hospit_de_jour}} 
    <img style="float: right;" title="{{mb_value object=$bilan field=_demi_journees}}" src="modules/ssr/images/dj-{{$bilan->_demi_journees}}.png" />
  {{/if}}
	
  {{assign var=libelle value=$sejour->libelle|upper}}
	{{assign var=color value=$colors.$libelle}}
	{{if $color->color}}
	  <div class="motif-color" style="background-color: #{{$color->color}};" title="{{$sejour->libelle}}"></div>
	{{else}}
    {{mb_value object=$sejour field=libelle}}
	{{/if}}
</div>

