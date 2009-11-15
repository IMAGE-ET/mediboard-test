{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="print">
  <tr><th colspan="2">Dr {{$admission->_ref_chir->_view}}</th></tr>
  <tr><th colspan="2"><!-- Adresse 1 --></th></tr>
  <tr><th colspan="2"><!-- Adresse 2 --></th></tr>
  <tr><th colspan="2"><!-- CP, ville --></th></tr>
  <tr><th colspan="2"><!-- Tel --></th></tr>
  <tr><th class="title" colspan="2"><a href="#" onclick="window.print()">Supplément d'honoraire</a></th></tr>

  <tr>
  	{{assign var=sejour value=$admission->_ref_sejour}}
    <th>{{mb_label object=$sejour field=patient_id}}</th>
    <td>{{mb_value object=$sejour field=patient_id}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$admission field=_datetime}}</th>
    <td>{{mb_value object=$admission field=_datetime}}</td>
  </tr>
  
  {{if $admission->libelle}}
  <tr>
    <th>{{mb_label object=$admission field=libelle}}</th>
    <td>{{mb_value object=$admission field=libelle}}</td>
  </tr>
  {{/if}}
  
  {{foreach from=$admission->_ext_codes_ccam item=_acte}}
  <tr>
    <th>{{tr}}CActeCCAM{{/tr}}</th>
    <td class="text"><strong>[{{$_acte->code}}]</strong> {{$_acte->libelleLong}}</td>
  </tr>
  {{/foreach}}

  <tr>
    <th>{{mb_label object=$admission field=cote}}</th>
    <td>{{mb_value object=$admission field=cote}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$admission field=depassement}}</th>
    <td>{{mb_value object=$admission field=depassement}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$admission field=depassement_anesth}}</th>
    <td>{{mb_value object=$admission field=depassement_anesth}}</td>
  </tr>

  <tr>
    <th>Signature du patient</th>
  </tr>

</table>