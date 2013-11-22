{{*
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_default var=object value=""}}
{{mb_default var=class value="CPatient"}}

<table class="tbl">
  <tr>
    <th colspan="3" class="title">{{tr}}{{$class}}{{/tr}}</th>
  </tr>
  <tr>
    <th>Nb</th>
    <th>Champ</th>
    <th>Propriétés</th>
  </tr>
  {{foreach from=$object key=context item=_object_specs}}
    {{foreach from=$_object_specs name=specs item=_spec}}
      <tr>
        <td>{{$smarty.foreach.specs.index}}</td>
        <td>
          {{if $context != "main"}}
            {{mb_label class=$class field=$context}}
          {{/if}}
          {{mb_label class=$_spec->className field=$_spec->fieldName}}
        </td>
        <td>
          {{$_spec->getLitteralDescription()}}
        </td>
      </tr>
    {{/foreach}}
  {{/foreach}}
</table>