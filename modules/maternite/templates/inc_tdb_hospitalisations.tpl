{{*
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Maternite
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<table class="tbl">
  <tr>
    <th class="title" colspan="10">{{$listSejours|@count}} hospitalisation(s) au {{$date|date_format:$conf.date}}</th>
  </tr>
  <tr>
    <th class="narrow">{{mb_title class=CAffectation field=lit_id}}</th>
    <th class="narrow">{{mb_title class=CSejour field=entree}}</th>
    <th>{{mb_title class=CGrossesse field=parturiente_id}}</th>
    <th>{{mb_title class=CGrossesse field=datetime_accouchement}}</th>
    <th class="narrow">Actions</th>
  </tr>

  {{foreach from=$listSejours item=_sejour}}
  <tr>
    <td>{{mb_value object=$_sejour->_ref_curr_affectation field=lit_id}}</td>
    <td>{{mb_value object=$_sejour field=entree}}</td>
    <td>{{mb_value object=$_sejour->_ref_grossesse field=parturiente_id}}</td>
    <td>{{mb_value object=$_sejour->_ref_grossesse field=datetime_accouchement}}</td>
    <td class="button"></td>
  </tr>
  {{/foreach}}
</table>