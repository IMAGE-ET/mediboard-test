{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
<tr>

  <th class="title" colspan="2">
  <div style="float: right">{{$dateTime|date_format:"%d/%m/%Y %Hh%M"}}</div>
  Dossier de soin (Bilan des administrations et transmissions)
  </th>
</tr>
<tr>
  <td>
    {{$sejour->_ref_patient->_view}}
  </td>
  <td>
  </td>
</tr>
<tr>
  <th>Libelle</th>
  <th>Quantite: administration</th>
</tr>
{{foreach from=$dossier key=date item=lines_by_cat}}
  <tr>
    <th colspan="2">{{$date|date_format:"%d/%m/%Y"}}</th>
  </tr>
  {{foreach from=$lines_by_cat key=chap item=lines}}
  <tr>
    <td colspan="2">
      <strong>
        {{if $chap == "medicament"}}
          Médicament
        {{elseif $chap == "perfusion"}}
          Perfusions
        {{else}}
          {{tr}}CCategoryPrescription.chapitre.{{$chap}}{{/tr}}
        {{/if}}
      </strong>
    </td>
  </tr>
    {{foreach from=$lines key=line_id item=administrations}}
      {{if $chap == "perfusion"}}
        {{assign var=_perfusion value=$administrations}}
        <tr>
          <td class="text">
              {{$_perfusion->_view}}
              {{if $_perfusion->duree}} pendant {{$_perfusion->duree}} heures{{/if}}
              {{if $_perfusion->time_debut}} à partir de {{mb_value object=$_perfusion field="time_debut"}}{{/if}} 
          </td>
          <td>
          <ul>
          {{foreach from=$_perfusion->_ref_lines item=_line_med}}
            <li>{{$_line_med->_view}}</li>
          {{/foreach}}
          </ul>
          </td>
        </tr>
      {{else}}
	      {{if $chap == "medicament"}}
	        {{assign var=line value=$lines_med.$line_id}}
	      {{else}}
	        {{assign var=line value=$lines_elt.$line_id}}
	      {{/if}}
	      <tr>
	        <td>
	          {{$line->_view}}
	        </td>
	        <td>  
	          {{foreach from=$administrations key=quantite item=_administrations_by_quantite}}
	            {{$quantite}} 
	            {{if $line->_class_name == "CPrescriptionLineMedicament"}}
	              {{$line->_ref_produit->libelle_unite_presentation}}
	            {{else}}
	              {{$line->_unite_prise}}
	            {{/if}}: 
	            {{foreach from=$_administrations_by_quantite item=_administration}}
	            {{$_administration->dateTime|date_format:$dPconfig.time}}
	            {{/foreach}}
	            <br />
	          {{/foreach}}
	        </td>  
	        </tr>
      {{/if}}  
    {{/foreach}}
  {{/foreach}}
{{/foreach}}
</table>
<table class="tbl">
  <tr>
    <th colspan="4">Transmissions</th>
  </tr>
  <tr>
    <th>Date</th>
    <th>Utilisateur</th>
    <th>Cible</th>
    <th>Transmission</th>
  </tr>
    {{foreach from=$sejour->_ref_transmissions item=_transmission}}
  <tr>
    <td>{{$_transmission->date|date_format:"%d/%m/%Y %Hh%M"}}</td>
    <td>{{$_transmission->_ref_user->_view}}</td>
    <td class="text">{{if $_transmission->object_id}}{{$_transmission->_ref_object->_view}}{{/if}}</td>
    <td class="text">{{$_transmission->text}}</td>
  </tr>
  {{/foreach}}
</table>