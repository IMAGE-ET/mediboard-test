{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage sherpa
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<!-- Filter -->
<form name="Filter" action="?m={{$m}}" method="get" onsubmit="return checkForm(this)">

<input type="hidden" name="do" value="1" />
<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />

<table class="form">
  <tr>
    <th class="category" colspan="10">Export d'actes vers le T2A</th>
  </tr>

  <tr>
    <th>{{mb_label object=$filter field=_num_dossier}}</th>
    <td>{{mb_field object=$filter field=_num_dossier}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$filter field=_date_sortie}}</th>
    <td class="date">{{mb_field object=$filter field=_date_sortie form=Filter register=true}}</td>
  </tr>

  <tr>
    <td class="button" colspan="10">
      <button class="tick" type="submit">
        Exporter les actes
      </button>
    </td>
  </tr>
</table>

</form>

{{if $do}}
{{include file=inc_list_export_actes.tpl}}
{{else}}
<div class="big-info">
  Il est nécessaire de valider l'export pour le réaliser.
  Merci de cliquer sur <strong>Exporter les actes</strong> après avoir choisi :
  <dl>
    <dt>soit une <em>date</em></dt>
    <dd>Pour exporter les actes de tous les séjours ayant une sortie réelle ce jour.</dd>
    <dt>soit un <em>numéro de dossier</em></dt>
    <dd>Pour exporter les actes spécifiques à un séjour en particulier.</dd>
  </dl>
</div>
{{/if}}

