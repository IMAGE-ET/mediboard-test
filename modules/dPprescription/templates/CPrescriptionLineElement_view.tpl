{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var="line" value=$object}}
{{assign var=chapitre value=$line->_ref_element_prescription->_ref_category_prescription->chapitre}}
<table class="tbl">
  <tr>
    <th colspan="3">{{$line->_view}}</th>
  </tr>
  {{if $chapitre != "dmi"}}
  <tr>
    {{if !$line->fin}}
    <td>
      {{mb_label object=$line field="debut"}}: {{mb_value object=$line field="debut"}} 
			{{if $line->time_debut}}
  			à {{mb_value object=$line field="time_debut"}}
			{{/if}}
    </td>
   {{if $chapitre != "anapath" && $chapitre != "imagerie" && $chapitre != "consult"}}
    <td>
      {{mb_label object=$line field="duree"}}: 
        {{if $line->duree && $line->unite_duree}}
          {{mb_value object=$line field="duree"}}  
          {{mb_value object=$line field="unite_duree"}}
        {{else}}
        -
        {{/if}}
    </td>
    <td>
      {{mb_label object=$line field="_fin"}}: {{mb_value object=$line field="_fin"}}
    </td>
    {{/if}}
    {{else}}
    <td colspan="3">
      {{mb_label object=$line field="fin"}}: {{mb_value object=$line field="fin"}}
    </td>
    {{/if}}
  </tr>
  {{/if}}
  {{if $chapitre != "dmi" && $chapitre != "anapath"}}
  <tr>
    <td colspan="3">
    Posologie:<br />
			<ul>
			{{foreach from=$line->_ref_prises item=_prise}}
			  <li>{{$_prise->_view}}</li>
			{{/foreach}}
			</ul>
    </td>
  </tr>
  {{/if}}
  <tr>
    <td colspan="3">   
      {{mb_label object=$line field="commentaire"}}:
      {{if $line->commentaire}}
        {{mb_value object=$line field="commentaire"}}
      {{else}}
        -
      {{/if}}
    </td>
  </tr>
  <tr>
    <td colspan="3">
      {{mb_label object=$line field="ald"}}:
      {{if $line->ald}}
        Oui
      {{else}}
        Non
      {{/if}}
    </td>
  </tr>
  <tr>
    <td colspan="3">
      Praticien: 
      {{$line->_ref_praticien->_view}}
    </td>
  </tr>
  <tr>
    <td colspan="3">
      Exécutant: 
      {{if $line->_ref_executant}}
        {{$line->_ref_executant->_view}}
      {{/if}}
    </td>
  </tr>
  {{if $line->_ref_transmissions|@count}}
  <tr>
    <th colspan="3">Transmissions</th>
  </tr>
  {{foreach from=$line->_ref_transmissions item=_transmission}}
  <tr>
    <td colspan="3">
      {{$_transmission->_view}} le {{$_transmission->date|date_format:$dPconfig.datetime}}:<br /> {{$_transmission->text}}
    </td>
  </tr>
  {{/foreach}}
  {{/if}}
  <tr>
    <td>Patient:</td>
    <td colspan="2">{{$line->_ref_prescription->_ref_patient->_view}}</td>
  </tr>
  <tr>
    <td>Contexte:</td>
    <td colspan="2">{{$line->_ref_prescription->_ref_object->_view}}</td>
  </tr>
	{{if $line->_ref_element_prescription->description}}
	<tr>
		<td>{{mb_label object=$line->_ref_element_prescription field=description}}</td>
		<td colspan="2" class="text">{{mb_value object=$line->_ref_element_prescription field=description}}</td>
	</tr>
	{{/if}}
</table>