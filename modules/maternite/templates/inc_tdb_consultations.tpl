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
    <th class="title" colspan="10">{{$listConsults|@count}} Consultation(s) le {{$date|date_format:$conf.date}}</th>
  </tr>
  <tr>
    <th class="narrow">{{mb_title class=CConsultation field=heure}}</th>
    <th>{{mb_title class=CConsultation field=_praticien_id}}</th>
    <th>{{mb_title class=CGrossesse field=parturiente_id}}</th>
  </tr>

  {{foreach from=$listConsults item=_consult}}
    <tr>
      <td>{{mb_value object=$_consult field=heure}}</td>
      <td>{{mb_value object=$_consult->_ref_plageconsult field=chir_id}}</td>
      <td>{{mb_value object=$_consult->_ref_grossesse field=parturiente_id}}</td>
    </tr>
  {{/foreach}}
</table>