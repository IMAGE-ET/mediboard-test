{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfig-CActe" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  
  <table class="form">
    <tr>
      <th class="title" colspan="2">{{tr}}config-{{$m}}-{{$class}}{{/tr}}</th>
    </tr>
    {{mb_include module=system template=inc_config_bool var=active}}
    {{mb_include module=system template=inc_config_bool var=active_salle_reveil}}
    
    <tr>
      <th class="title" colspan="2">Cocher la bonne réponse par défaut dans les checklists de : </th>
    </tr>
    <tr>
      <td colspan="2">
        <div class="small-info">
          Choisir "Oui" signifie que la réponse cochée par défaut est celle qui serait choisie si le point à vérifier est positif.
          <br />
          <strong>Attention, une réponse positive peut être "Non" si par exemple la question est "Risque de saignement important".</strong>
        </div>
      </td>
    </tr>
    {{mb_include module=system template=inc_config_bool var=default_good_answer_COperation}}
    {{mb_include module=system template=inc_config_bool var=default_good_answer_CSalle}}
    {{mb_include module=system template=inc_config_bool var=default_good_answer_CBlocOperatoire}}
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>