{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage ameli
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

<form name="formDureeIndicative" action="?" method="post" onsubmit="return false;">
  <table class="tbl">
    <tr>
      <th>{{tr}}TypeEmploi{{/tr}}</th>
      <th colspan="2">{{tr}}CDureeIndicativeArretTravail{{/tr}}</th>
    </tr>
    {{if $duree}}
      {{foreach from=$duree->criteres item=_critere}}
        {{mb_include module=ameli template=inc_critere_arret_travail critere=$_critere depth=0}}
      {{foreachelse}}
        <tr>
          <td colspan="3">{{tr}}CDureeIndicativeArretTravail.none{{/tr}}</td>
        </tr>
      {{/foreach}}
    {{else}}
      <tr>
        <td colspan="3">{{tr}}CDureeIndicativeArretTravail.none{{/tr}}</td>
      </tr>
    {{/if}}
  </table>
</form>