{{*
 * $Id$
 *  
 * @category dPCabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<table class="main tbl" id="old_consult">
  <tr>
    <th class="title" colspan="7">Liste des consultations d'anesthésie pour {{$patient->_view}}</th>
  </tr>
  <tr>
    <th class="category"></th>
    <th class="category"></th>
    <th class="category">{{mb_label class=CConsultAnesth field="mallampati"}}</th>
    <th class="category">{{mb_label class=CConsultAnesth field="bouche"}}</th>
    <th class="category">{{mb_label class=CConsultAnesth field="distThyro"}}</th>
    <th class="category">{{mb_label class=CConsultAnesth field="mob_cervicale"}}</th>
    <th></th>
  </tr>
  {{foreach from=$dossiers_anesth item=_dossier_anesth}}
    <tr>
      {{assign var=consultation value=$_dossier_anesth->_ref_consultation}}
      <td>{{mb_value object=$consultation field="_date"}}</td>
      <td>
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$consultation->_ref_praticien}}
      </td>
      <td>{{mb_value object=$_dossier_anesth field="mallampati"}}</td>
      <td>{{mb_value object=$_dossier_anesth field="bouche"}}</td>
      <td>{{mb_value object=$_dossier_anesth field="distThyro"}}</td>
      <td>{{mb_value object=$_dossier_anesth field="mob_cervicale"}}</td>
      <td class="button">
        <button class="tick" type="submit"
                {{if !$_dossier_anesth->mallampati && !$_dossier_anesth->bouche && !$_dossier_anesth->distThyro && !$_dossier_anesth->mob_cervicale}}disabled{{/if}}
                onclick="assignDataOldConsultAnesth('{{$_dossier_anesth->mallampati}}', '{{$_dossier_anesth->bouche}}', '{{$_dossier_anesth->distThyro}}', '{{$_dossier_anesth->mob_cervicale}}');">{{tr}}common-action-Get{{/tr}}</button>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty" colspan="7">{{tr}}CConsultAnesth.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>
