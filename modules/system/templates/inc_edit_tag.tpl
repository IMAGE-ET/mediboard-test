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

<form method="post" name="edit_tag_{{$tag->_guid}}" onsubmit="return onSubmitFormAjax(this, {onComplete: Control.Modal.close})">
  <input type="hidden" name="del" value="0"/>
  {{mb_key object=$tag}}
  {{mb_class object=$tag}}
  {{mb_field object=$tag field=object_class hidden=1}}

  <table class="form">
  {{mb_include module=system template=inc_form_table_header object=$tag}}

    <tr>
      <th style="width:50%;">{{mb_label object=$tag field=name}}</th>
      <td>{{mb_field object=$tag field=name}}</td>
    </tr>

    <tr>
      <th>Tag</th>
      <td>{{mb_field object=$tag field=parent_id form="edit_tag_`$tag->_guid`" autocomplete="true,1,50,true,true"}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$tag field=color}}</th>
      <td>
        <script>
          ColorSelector.init = function(){
            this.sForm  = "edit_tag_{{$tag->_guid}}";
            this.sColor = "color";
            this.sColorView = "edit_tag_{{$tag->_guid}}_color";
            this.pop();
          }
        </script>
        {{mb_field object=$tag field=color style="background: #`$tag->color`;" readonly="readonly"}}
        <button type="button" class="search notext" onclick="ColorSelector.init()">
          {{tr}}Choose{{/tr}}
        </button>
      </td>
    </tr>

    <tr>
      <td class="button" colspan="2">
        {{if $tag->_id}}
          <button class="modify">{{tr}}Save{{/tr}}</button>
          <button class="trash" type="button" onclick="confirmDeletion(this.form, {typeName:'le tag',objName:'{{$tag->_view|smarty:nodefaults|JSAttribute}}', ajax:1}, {onComplete: Control.Modal.close});">{{tr}}Delete{{/tr}}</button>
        {{else}}
          <button class="new">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>

    {{if $tag->_id}}
      <tr>
        <td colspan="2">
          <div class="small-info">{{mb_label object=$tag field=_nb_items}} : {{mb_value object=$tag field=_nb_items}}</div>
        </td>
      </tr>
    {{/if}}
  </table>

</form>