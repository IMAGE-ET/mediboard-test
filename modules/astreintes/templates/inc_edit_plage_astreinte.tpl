{{* $Id:*}}

{{*
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
  refreshlistPhone = function() {
    var form = getForm('editplage');
    var user_id = $V(form.user_id);

    var url = new Url("astreintes", "ajax_list_phones");
    url.addParam("user_id", user_id);
    url.requestUpdate("list_phones");

  };

  setPhone = function(phone_value) {
    var form = getForm('editplage');
    $V(form.phone_astreinte, phone_value);
  };

  Main.add(function() {
    refreshlistPhone();
  });
</script>

<table class="main">
  <tr>
    <td>
      <form name="editplage" action="" method="post" onsubmit="return onSubmitFormAjax(this,{onComplete: Control.Modal.close}); ">
        {{mb_key object=$plageastreinte}}
        <input type="hidden" name="dosql" value="do_plageastreinte_aed" />
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$a}}" />
        <input type="hidden" name="group_id" value="{{$plageastreinte->group_id}}" />
        <table class="form">
          {{mb_include module=system template=inc_form_table_header object=$plageastreinte}}

          <tr>
            <th>
              {{mb_label object=$plageastreinte field="user_id"}}
            </th>
            <td>
              <select name="user_id" onchange="refreshlistPhone();">
                <option value="">{{tr}}CMediusers.all{{/tr}}</option>
                {{mb_include module=mediusers template=inc_options_mediuser list=$users selected=$plageastreinte->user_id}}
              </select>
            </td>
          </tr>
          <tr>
            <th>{{mb_label object=$plageastreinte field="libelle"}}</th>
            <td>{{mb_field object=$plageastreinte field="libelle"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$plageastreinte field="group_id"}}</th>
            <td>{{$plageastreinte->_ref_group}}</td>
          </tr>

          <tr>
            <th>{{mb_label object=$plageastreinte field="type"}}</th>
            <td>{{mb_field object=$plageastreinte field="type"}}</td>
          </tr>

          <tr>
            <th>{{mb_label object=$plageastreinte field="start"}}</th>
            <td>{{mb_field object=$plageastreinte field="start" form="editplage" register="true"}}</td>
          </tr>

          <tr>
            <th>{{mb_label object=$plageastreinte field="end"}}</th>
            <td>{{mb_field object=$plageastreinte field="end" form="editplage" register="true"}}</td>
          </tr>

          <tr>
            <th>{{mb_label object=$plageastreinte field="phone_astreinte"}}</th>
            <td>{{mb_field object=$plageastreinte field="phone_astreinte" form="editplage"}}</td>
          </tr>

          <tr>
            <td colspan="6" class="button">
              <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>
              {{if $plageastreinte->_id}}
                <button class="trash" type="button"
                        onclick="confirmDeletion(this.form,{typeName:'la plage',objName:'{{$plageastreinte->_view|smarty:nodefaults|JSAttribute}}', ajax :true})">
                  {{tr}}Delete{{/tr}}
                </button>
              {{/if}}
            </td>
          </tr>
        </table>
        {{if @count($plageastreinte->_collisionList)}}
          <div class="small-warning">
            {{foreach from=$plageastreinte->_collisionList item=_collision}}
              {{$_collision}}
            {{/foreach}}
          </div>
        {{/if}}
      </form>
    </td>
    <td id="list_phones">

    </td>
  </tr>

</table>