{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage sherpa
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<!-- Liste d'actes exportés -->
<table class="tbl">
  {{if $filter->_date_sortie}}
  <tr>
    <th class="title" colspan="12">
      Envoi d'actes pour les séjours sortis le : 
      {{$filter->_date_sortie|date_format:$dPconfig.longdate}}
    </th>
  </tr>
  {{/if}}

  <tr>
    <td>{{mb_title object=$acte_ccam field=executant_id}}</td>
    <td>{{mb_title object=$acte_ccam field=execution}}</td>
    <td>{{mb_title object=$acte_ccam field=code_acte}}</td>
    <td>{{mb_title object=$acte_ccam field=code_activite}}</td>
    <td>{{mb_title object=$acte_ccam field=code_phase}}</td>
    <td>{{mb_title object=$acte_ccam field=modificateurs}}</td>
    <td>{{mb_title object=$acte_ccam field=rembourse}}</td>
    <td>{{mb_title object=$acte_ccam field=code_association}}</td>
    <td>{{mb_title object=$acte_ccam field=montant_base}}</td>
    <td>{{mb_title object=$acte_ccam field=montant_depassement}}</td>
    <td>{{mb_title object=$acte_ccam field=signe}}</td>
    <td>Statut de l'envoi</td>
    
  </tr>

	{{foreach from=$sejours key=sejour_id item=_sejour}}
  <tr>
    <th class="title" colspan="11">
	    {{$_sejour->_view}}
	    <strong>[{{$_sejour->_num_dossier}}]</strong>
	    <br />Dr {{$_sejour->_ref_praticien->_view}}
    </th>

    <td>
    	{{if $_sejour->_num_dossier == "-"}}
    	<div class="error">
        Le séjour #{{$_sejour->_id}} n'a pas de numéro de dossier
        <br />Exécution interrompue. 
      </div>
			{{else}}
      <div class="warning">
        {{tr}}CSpEntCCAM{{/tr}} : {{$delEntCCAM.$sejour_id}} suppressions pour ce séjour 
      </div>
      <div class="warning">
        {{tr}}CSpDetCCAM{{/tr}} : {{$delDetCCAM.$sejour_id}} suppressions pour ce séjour 
      </div>
      <div class="warning">
        {{tr}}CSpDetCIM{{/tr}} : {{$delDetCIM.$sejour_id}} suppressions pour ce séjour 
      </div>
      <div class="warning">
        {{tr}}CSpNGAP{{/tr}} : {{$delActNGAP.$sejour_id}} suppressions pour ce séjour 
      </div>
			{{include file="inc_export_entccam.tpl" _codable=$_sejour}}
			{{/if}}
    </td>
  </tr>
  
  <!-- Actes du séjour -->
 	{{if $_sejour->_num_dossier != "-"}}
  {{include file="inc_export_detcim.tpl" _codable=$_sejour}}
  {{foreach from=$_sejour->_ref_actes item=_acte}}
	{{include file="inc_export_acte.tpl" _acte=$_acte}}
  {{foreachelse}}
  <tr>
    <td colspan="12"><em>Pas d'acte d'hospitalisation</em></td>
  </tr>
  {{/foreach}}

  {{foreach from=$_sejour->_ref_operations item=_operation}}
  <tr>
    <th colspan="11">
	    Intervention du {{$_operation->_datetime}} 
	    {{if $_operation->libelle}}
	    <em>[{{$_operation->libelle}}]</em>
	    {{/if}}
	    <br />Dr {{$_operation->_ref_chir->_view}}
	    &mdash; [IDINTERV = {{$_operation->_idinterv}}]
    </th>
    <td>
			{{include file="inc_export_entccam.tpl" _codable=$_operation}}
		</td>    
  </tr>

  {{include file="inc_export_detcim.tpl" _codable=$_operation}}
  {{foreach from=$_operation->_ref_actes item=_acte}}
	{{include file="inc_export_acte.tpl" _acte=$_acte}}
  {{foreachelse}}
  <tr>
    <td colspan="11"><em>Pas d'acte d'intervention</em></td>
  </tr>
  {{/foreach}}
  {{/foreach}}
  {{/if}}

	{{/foreach}}
</table>