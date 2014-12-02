{{*
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<table class="main tbl" id="old_consult">
  <tr>
    <th class="title" colspan="6">Liste des consultations d'anésthésie pour {{$patient->_view}}</th>
  </tr>
  <tr>
    <th class="category"></th>
    <th class="category"></th>
    <th class="category">{{mb_label class=CConsultAnesth field="mallampati"}}</th>
    <th class="category">{{mb_label class=CConsultAnesth field="bouche"}}</th>
    <th class="category">{{mb_label class=CConsultAnesth field="distThyro"}}</th>
    <th></th>
  </tr>
  {{foreach from=$consultations_anesth item=_consult_anesth}}
    <tr>
      <td>{{$_consult_anesth->_ref_consultation->_ref_plageconsult->date|date_format:'%d/%m/%Y'}}</td>
      <td>
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_consult_anesth->_ref_consultation->_ref_praticien}}
      </td>
      <td>{{mb_value object=$_consult_anesth field="mallampati"}}</td>
      <td>{{mb_value object=$_consult_anesth field="bouche"}}</td>
      <td>{{mb_value object=$_consult_anesth field="distThyro"}}</td>
      <td class="button">
        <button class="tick" type="submit"
                {{if !$_consult_anesth->mallampati && !$_consult_anesth->bouche && !$_consult_anesth->distThyro}}disabled{{/if}}
                onclick="assignDataOldConsultAnesth('{{$_consult_anesth->mallampati}}', '{{$_consult_anesth->bouche}}', '{{$_consult_anesth->distThyro}}');">{{tr}}common-action-Get{{/tr}}</button>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty">{{tr}}CConsultAnesth.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>
