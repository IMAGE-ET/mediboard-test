{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="form" id="admission">
  <tr><th colspan="2">Dr {{$admission->_ref_chir->_view}}</th></tr>
  <tr><th colspan="2"><!-- Adresse 1 --></th></tr>
  <tr><th colspan="2"><!-- Adresse 2 --></th></tr>
  <tr><th colspan="2"><!-- CP, ville --></th></tr>
  <tr><th colspan="2"><!-- Tel --></th></tr>
  <tr><th class="title" colspan="2"><a href="#" onclick="window.print()">Supplément d'honoraire</a></th></tr>

  <tr>
    <th>Nom / Prénom du patient :</th>
    <td>{{$admission->_ref_sejour->_ref_patient->_view}}</td>
  </tr>

  <tr>
    <th>Date d'intervention :</th>
    <td>{{$admission->_ref_plageop->_day}}/{{$admission->_ref_plageop->_month}}/{{$admission->_ref_plageop->_year}}</td>
  </tr>
  
  {{if $admission->libelle}}
  <tr>
    <th>Libellé :</th>
    <td class="text">{{$admission->libelle}}</td>
  </tr>
  {{/if}}
  
  {{foreach from=$admission->_ext_codes_ccam item=curr_acte}}
  <tr>
    <th>Acte médical :</th>
    <td class="text">{{$curr_acte->libelleLong}} <i>({{$curr_acte->code}})</i></td>
  </tr>
  {{/foreach}}

  <tr>
    <th>Côté: </th>
    <td>{{tr}}COperation.cote.{{$admission->cote}}{{/tr}}</td>
  </tr>
  
  <tr>
    <th>Dépassement d'honoraires: </th><td>{{$admission->depassement}} {{$dPconfig.currency_symbol}}</td>
  </tr>

  <tr>
    <th>Signature du patient</th>
  </tr>

</table>