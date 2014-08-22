{{*
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<table class="layout">
  <tr>
    <td>
      {{mb_field object=$consult_anesth field=plus_de_55_ans typeEnum=checkbox onchange="verifIntubDifficileAndSave(this.form);"}}
      {{mb_label object=$consult_anesth field=plus_de_55_ans}}
    </td>
    <td>
      {{mb_field object=$consult_anesth field=edentation typeEnum=checkbox onchange="verifIntubDifficileAndSave(this.form);"}}
      {{mb_label object=$consult_anesth field=edentation}}
    </td>
    <td>
      {{mb_field object=$consult_anesth field=barbe typeEnum=checkbox onchange="verifIntubDifficileAndSave(this.form);"}}
      {{mb_label object=$consult_anesth field=barbe}}
    </td>
  </tr>
  <tr>
    <td>
      {{mb_field object=$consult_anesth field=imc_sup_26 typeEnum=checkbox onchange="verifIntubDifficileAndSave(this.form);"}}
      {{mb_label object=$consult_anesth field=imc_sup_26}}
    </td>
    <td>
      {{mb_field object=$consult_anesth field=ronflements typeEnum=checkbox onchange="verifIntubDifficileAndSave(this.form);"}}
      {{mb_label object=$consult_anesth field=ronflements}}
    </td>
  </tr>
</table>