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
    <th colspan="4" class="title">{{tr}}{{$class}}{{/tr}} <button class="forms" onclick="Modal.open('import_csv_line_{{$class}}', {width:'-30', showClose:true})">En-tête CSV</button></th>
  </tr>
  <tr>
    <th>#</th>
    <th>Champ</th>
    <th>Propriétés</th>
    <th>Description</th>
  </tr>
  {{assign var=iterator value=0}}
  {{foreach from=$object key=context item=_object_specs}}
    {{foreach from=$_object_specs name=specs item=_spec}}
      {{assign var=class_name value=$_spec->className}}
      {{assign var=field_name value=$_spec->fieldName}}
      {{assign var=desc value="CAppUI::tr"|static_call:"$class_name-$field_name-desc"}}
      {{assign var=trad value="CAppUI::tr"|static_call:"$class_name-$field_name"}}
      <tr>
        <th><span title="{{$_spec->fieldName}}">{{$iterator}}</span></th>
        <td>
          {{if $_spec->notNull}}<strong>{{/if}}
          {{if $context != "main"}}
            {{mb_label class=$class field=$context}}
          {{/if}}
          {{mb_label class=$_spec->className field=$_spec->fieldName}}
          {{if $_spec->notNull}}*</strong>{{/if}}
        </td>
        <td class="text">
          {{$_spec->getLitteralDescription()|smarty:nodefaults}}
        </td>
        <td class="text compact">
          {{if $trad != $desc}}
            {{$desc}}
          {{/if}}
        </td>
        {{assign var=iterator value=$iterator+1}}
      </tr>
    {{/foreach}}
  {{/foreach}}
</table>
<!-- csv line for help -->
<div id="import_csv_line_{{$class}}" style="display:none;">

  <textarea>{{foreach from=$object key=context item=_object_specs}}{{foreach from=$_object_specs name=specs item=_spec}}{{assign var=class_name value=$_spec->className}}{{assign var=field_name value=$_spec->fieldName}}"{{if $context != "main"}}{{tr}}{{$class_name}}{{/tr}} {{/if}}{{$field_name}}";{{/foreach}}{{/foreach}}</textarea>
</div>