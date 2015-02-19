{{*
 * $Id$
 *  
 * @category Pmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<!-- Onglet dossiers en cours volet Interventions planifiées -->

{{mb_include module=system template=inc_pagination total=$countOp current=$pageOp change_page="changePageOp" step=$step}}


<table class=" main tbl">
  <tr>
    <th class="title" colspan="9">
      {{$date|date_format:$conf.longdate}}
    </th>
  </tr>
  <tr>
    <th>{{mb_title class=Coperation field=facture}}</th>
    <th class="narrow">{{mb_title class=CSejour field=_NDA}}</th>
    <th>{{mb_label class=Coperation field=chir_id}}</th>
    <th>{{mb_label class=CSejour field=patient_id}}</th>
    <th>{{mb_label class=Coperation field=time_operation}}</th>
    <th>
      {{mb_label class=Coperation  field=libelle}} +
      {{mb_label class=Coperation field=codes_ccam}}
    </th>
    <th>Docs</th>
    <th>{{mb_title class=Coperation field=labo}}</th>
    <th>{{mb_title class=Coperation field=anapath}}</th>
  </tr>
  <tbody>
    {{foreach from=$operations item=_operation}}
      {{mb_include template=current_dossiers/inc_current_interv_line}}
    {{/foreach}}
  </tbody>
</table>