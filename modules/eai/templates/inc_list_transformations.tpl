{{*
 * View transformations rules
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
*}}

{{mb_script module=eai script=transformation ajax=true}}

{{assign var=event_name value=$event|get_class}}

<div>
  <button onclick="EAITransformation.link('{{$event_name}}', '{{$actor->_guid}}');" class="button new">
    {{tr}}CEAITransformation-title-create{{/tr}}
  </button>
</div>

<table class="main tbl">
  <tr>
    <th colspan="14" class="title">
      {{tr}}CEAITransformation.all{{/tr}}
    </th>
  </tr>
  <tr>
    <th class="narrow button"></th>
    <th class="category narrow"> {{mb_title class=CEAITransformation field=standard}} </th>
    <th class="category narrow"> {{mb_title class=CEAITransformation field=domain}} </th>
    <th class="category narrow"> {{mb_title class=CEAITransformation field=profil}} </th>
    <th class="category narrow"> {{mb_title class=CEAITransformation field=message}} </th>
    <th class="category narrow"> {{mb_title class=CEAITransformation field=transaction}} </th>
    <th class="category narrow"> {{mb_title class=CEAITransformation field=version}} </th>
    <th class="category narrow"> {{mb_title class=CEAITransformation field=extension}} </th>
    <th class="category narrow"> {{mb_title class=CEAITransformation field=active}} </th>
    <th class="category narrow"> {{mb_title class=CEAITransformation field=rank}} </th>
  </tr>

  {{foreach from=$transformations item=_transformation}}
    <tr>
      <td></td>
      <td>{{mb_value object=$_transformation field=standard}}</td>
      <td>{{mb_value object=$_transformation field=domain}}</td>
      <td>{{mb_value object=$_transformation field=profil}}</td>
      <td>{{mb_value object=$_transformation field=message}}</td>
      <td>{{mb_value object=$_transformation field=transaction}}</td>
      <td>{{mb_value object=$_transformation field=version}}</td>
      <td>{{mb_value object=$_transformation field=extension}}</td>
      <td>{{mb_value object=$_transformation field=active}}</td>
      <td>{{mb_value object=$_transformation field=rank}}</td>
    </tr>
    {{foreachelse}}
    <tr><td class="emtpy" colspan="14">{{tr}}CEAITransformation.none{{/tr}}</td></tr>
  {{/foreach}}
</table>