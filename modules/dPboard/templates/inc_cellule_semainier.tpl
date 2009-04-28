{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var="plageInfos" value=$plageJour.$colonne}}

{{if is_string($plageJour.$colonne) && $plageJour.$colonne == "empty"}}
  <td class="empty"></td>
{{elseif is_string($plageJour.$colonne) && $plageJour.$colonne == "hours"}}
  <td class="empty" rowspan="4"></td>
{{elseif is_string($plageJour.$colonne) && $plageJour.$colonne == "full"}}
{{else}}
  <td class="nonEmpty{{$style}}" rowspan="{{$plageInfos->_nbQuartHeure}}">
  
  {{assign var="pct" value=$plageInfos->_fill_rate}}
  {{if $pct gt 100}}
  {{assign var="pct" value=100}}
  {{/if}}
  {{if $pct lt 50}}{{assign var="backgroundClass" value="empty"}}
  {{elseif $pct lt 90}}{{assign var="backgroundClass" value="normal"}}
  {{elseif $pct lt 100}}{{assign var="backgroundClass" value="booked"}}
  {{else}}{{assign var="backgroundClass" value="full"}}
  {{/if}}

  {{if $colonne=="plagesConsult"}}
    <a href="?m=dPcabinet&amp;tab=edit_consultation&amp;date={{$plageInfos->date}}" 
       onmouseover="viewItem(this, 'CPlageconsult',{{$plageInfos->_id}},'{{$curr_day}}')">
      {{if $plageInfos->libelle}}{{$plageInfos->libelle}}<br />{{/if}}
      {{$plageInfos->debut|date_format:$dPconfig.time}} - {{$plageInfos->fin|date_format:$dPconfig.time}}
    </a>
    <div class="progressBar">
      <div class="bar {{$backgroundClass}}" style="width: {{$pct}}%;"></div>
      <div class="text">{{$plageInfos->_affected}} / {{$plageInfos->_total}}</div>
    </div>
  {{else}}
    <a href="?m=dPplanningOp&amp;tab=vw_idx_planning&amp;date={{$plageInfos->date}}"
       onmouseover="viewItem(this, 'CPlageOp',{{$plageInfos->_id}},'{{$curr_day}}')">
      {{$plageInfos->_ref_salle->nom}}<br />
      {{$plageInfos->debut|date_format:$dPconfig.time}} - {{$plageInfos->fin|date_format:$dPconfig.time}}
    </a>
    <div class="progressBar">
      <div class="bar {{$backgroundClass}}" style="width: {{$pct}}%;"></div>
      <div class="text">{{$plageInfos->_nb_operations}} Op.</div>
    </div>    
  {{/if}}
  </td>            
{{/if}}