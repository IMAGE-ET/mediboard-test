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

{{assign var=event_name value=$event|get_class}}
{{assign var=message_name value=$message|get_class}}

<div>
  <button onclick="EAITransformation.link('{{$message_name}}', '{{$event_name}}', '{{$actor->_guid}}');" class="button new">
    {{tr}}CEAITransformation-title-create{{/tr}}
  </button>
</div>

<table class="main tbl">
  <tr>
    <th colspan="15" class="title">
      {{tr}}CEAITransformation.all{{/tr}}
    </th>
  </tr>
  <tr>
    <th class="narrow button"></th>
    <th class="category narrow"> {{mb_title class=CEAITransformation field=eai_transformation_id}} </th>
    <th class="category narrow"> {{mb_title class=CEAITransformation field=eai_transformation_rule_id}} </th>
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

  <tbody id="transformations">
    {{mb_include template="inc_list_transformations_lines"}}
  </tbody>
</table>