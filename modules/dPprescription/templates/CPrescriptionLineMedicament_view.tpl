{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var="line" value=$object}}
<table class="tbl">
  <tr>
    <th colspan="3"><a href="#" onclick="Prescription.viewProduit(null,'{{$line->code_ucd}}','{{$line->code_cis}}');">{{$line->_view}}</a></th>
  </tr>
  <tr>
    {{if !$line->fin}}
    <td>
      {{mb_label object=$line field="debut"}}: {{mb_value object=$line field="debut"}}
    </td>
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
    {{else}}
    <td colspan="3">
      {{mb_label object=$line field="fin"}}: {{mb_value object=$line field="fin"}}
    </td>
    {{/if}}
  </tr>
  {{if $line->date_arret}}
  <tr>
    <td colspan="3">
      <strong>
        {{mb_label object=$line field=date_arret}}: {{mb_value object=$line field=date_arret}}
        {{if $line->time_arret}}
          à {{mb_value object=$line field=time_arret}}
        {{/if}}
      </strong>
    </td>
  </tr>
  {{/if}}
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
    <td>
      {{if $line->_ref_prescription->object_class == "CDossierMedical"}}
        {{tr}}User{{/tr}}: 
      {{else}}
        {{mb_label object=$line field="praticien_id"}}:
      {{/if}}
    </td>
    <td colspan="2">
      {{$line->_ref_praticien->_view}}
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
	<tr>
		<td colspan="3" class="button"><button class="search" type="button" onclick="Prescription.viewProduit(null,'{{$line->code_ucd}}','{{$line->code_cis}}', 'two,posologie');">Posologie et mode d'administration</button></td>
	</tr>
</table>