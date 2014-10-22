{{*
 * $Id$
 *  
 * @category Urgences
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<tr>
  <th class="narrow text">{{mb_title class=CRPU field=_entree}}</th>
  <th>{{mb_title class=CRPU field=_patient_id}}</th>
  <th style="width: 8em;">{{mb_title class=CRPU field=ccmu}}</th>
  <th>{{mb_title class=CRPU field=diag_infirmier}}</th>
  <th class="narrow">Heure PeC</th>
  <th style="width: 8em;">{{mb_title class=CRPU field=_responsable_id}}</th>
  <th class="narrow">
    {{mb_title class=CSejour field=mode_sortie}}
    <br/> &amp;
    {{mb_title class=CRPU field=orientation}}
  </th>
  {{if $print_gemsa}}
    <th class="narrow">{{mb_title class=CRPU field=gemsa}}</th>
  {{/if}}
  <th class="narrow">{{mb_title class=CRPU field=_sortie}}</th>
</tr>