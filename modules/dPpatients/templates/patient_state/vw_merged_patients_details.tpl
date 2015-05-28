{{*
 * $Id$
 *
 * @category DPpatients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<h2 style="text-align: center;">
  {{$date}} &mdash; {{tr var1=$logs_count}}CPatientSate-msg-%d merged patients{{/tr}}
</h2>

<hr />

<div class="small-info">
  {{tr}}CPatientState-msg-Tags marked as trahs could be old patient IPP.{{/tr}}
</div>

<table class="main tbl">
  {{assign var=_count value=0}}
  {{foreach from=$logs key=_key item=_log}}
    {{math assign=_count equation='x + 1' x=$_count}}
    {{mb_include module=dPpatients template=patient_state/CPatientState_merged_view object=$_log}}
  {{/foreach}}
</table>