{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var="perf" value=$object}}
<table class="tbl">
  <tr>
    <th colspan="3">{{$perf->_view}}</th>
  </tr>
  <tr>
    <td>
      {{mb_label object=$perf field=type}} :
      {{mb_value object=$perf field=type}}
    </td>
  </tr>
  <tr>
    <td>
      {{mb_value object=$perf field=voie}}
    </td>
  </tr>
  <tr>
    <td>
      {{mb_label object=$perf field=vitesse}} :
      {{if $perf->vitesse}}
      {{mb_value object=$perf field=vitesse}} ml/h
      {{else}}
       - 
      {{/if}}
    </td>
  </tr>
  <tr>
    <td>
      {{mb_label object=$perf field=date_debut}} : 
      {{if $perf->date_debut}}
        {{mb_value object=$perf field=date_debut}} 
        {{if $perf->time_debut}}
          à {{mb_value object=$perf field=time_debut}}
        {{/if}}
      {{else}}
      -
      {{/if}}
    </td>
  </tr>
  {{if $perf->date_arret}}
  <tr>
    <td>
      <strong>
        {{mb_label object=$perf field=date_arret}}: {{mb_value object=$perf field=date_arret}} à {{mb_value object=$perf field=time_arret}}
      </strong>
    </td>
  </tr>
  {{/if}}
  <tr>
    <td>
      {{mb_label object=$perf field=duree}}
      {{mb_value object=$perf field=duree}} heures
    </td>
  </tr>
  <tr>
    <td>
      {{if $perf->_ref_lines|@count}}
      Produits :
      <ul>
        {{foreach from=$perf->_ref_lines item=_perf_line}}
          <li>{{$_perf_line->_view}}</li>
        {{/foreach}}
      </ul>
      {{else}}
      Aucun produit dans cette perfusion
      {{/if}}
    </td>
  </tr>
  {{if $perf->type == "PCA"}}
	  <tr>
	    <td>
	      {{mb_label object=$perf field=mode_bolus}}:
	      {{mb_value object=$perf field=mode_bolus}}
	    </td>
	  </tr>
	  {{if $perf->mode_bolus != "sans_bolus"}}
		  <tr>
		    <td>
		      {{mb_label object=$perf field=dose_bolus}}:
		      {{mb_value object=$perf field=dose_bolus}} mg
		    </td>
		  </tr>
		  <tr>
		    <td>
		      {{mb_label object=$perf field=periode_interdite}}:
		      {{mb_value object=$perf field=periode_interdite}} min
		    </td>
		  </tr>  
	  {{/if}}
  {{/if}}
  <tr>
    <td>
      {{mb_label object=$perf field=praticien_id}}:
      {{mb_value object=$perf field=praticien_id}}
    </td>
  </tr>
  {{if $perf->_ref_transmissions|@count}}
	  <tr>
	    <th colspan="3">Transmissions</th>
	  </tr>
	  {{foreach from=$perf->_ref_transmissions item=_transmission}}
	  <tr>
	    <td colspan="3">
	      {{$_transmission->_view}} le {{$_transmission->date|date_format:$dPconfig.datetime}}:<br /> {{$_transmission->text}}
	    </td>
	  </tr>
	  {{/foreach}}
  {{/if}}
</table>