{{*
 * $Id$
 *  
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<form name="editConfig-CPlageOp" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />

  <table class="form">
    <tr>
      <th colspan="2" class="category">Affichage</th>
    </tr>

    {{mb_include module=system template=inc_config_bool class=CPlageOp var=view_prepost_suivi}}
    {{mb_include module=system template=inc_config_bool class=CPlageOp var=chambre_operation}}
    {{mb_include module=system template=inc_config_bool class=suivi_salle var=view_tools_associated}}
    {{mb_include module=system template=inc_config_bool class=suivi_salle var=view_tools_required}}
    {{mb_include module=system template=inc_config_bool class=suivi_salle var=view_rques}}
    {{mb_include module=system template=inc_config_bool class=suivi_salle var=view_anesth_type}}

    <tr>
      <td class="button" colspan="100">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>