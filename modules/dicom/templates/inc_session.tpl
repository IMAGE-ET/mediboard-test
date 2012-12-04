{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr id="session_{{$object->_guid}}">
  <td class="narrow">
    {{if $object->sender == "[SELF]"}}
      <img src="images/icons/prev.png" alt="&lt;" />
    {{else}}
      <img src="images/icons/next.png" alt="&gt;" />
    {{/if}}
  </td>
  <td class="narrow">
    <form name="del{{$object->_guid}}" action="?" method="post">
      {{mb_class object=$object}}
      {{mb_key object=$object}}
      <input type="hidden" name="del" value="1"/>
      
      <button class="cancel notext" type="button" onclick="confirmDeletion(
        this.form,
        {
          ajax: 1,
          typeName: &quot;{{tr}}{{$object->_class}}.one{{/tr}}&quot;,
          objName:&quot;{{$object->_view|smarty:nodefaults|JSAttribute}}&quot;
        },
        { onComplete: DicomSession.refreshSessionsList.curry(getForm('sessionsFilters'))
      })">
      </button>
    </form>
  </td>
  <td class="narrow">
    <button type="button" onclick="DicomSession.viewSession('{{$object->_guid}}')" class="search">
      {{$object->_id|str_pad:6:'0':$smarty.const.STR_PAD_LEFT}}
    </button>
  </td>
  <td>
    <label title='{{mb_value object=$object field="begin_date"}}'>
      {{mb_value object=$object field="begin_date" format=relative}}
    </label>
  </td>
  <td>
    <label title='{{mb_value object=$object field="end_date"}}'>
      {{mb_value object=$object field="end_date" format=relative}}
    </label>
  </td>
  <td>
    {{mb_value object=$object field="_duration"}}
  </td>
  <td class="narrow">
    {{if $object->sender eq "[SELF]"}}
       <label title='[SELF]' style="font-weight:bold">
        [SELF]
       </label>
    {{else}}
      {{assign var=sender value=$object->_ref_actor}}
      <a href="?m=eai&tab=vw_idx_interop_actors#interop_actor_guid={{$sender->_guid}}">
        {{$sender->_view}}
      </a>
    {{/if}}
  </td>
  <td class="narrow">
    {{if $object->sender eq "[SELF]"}}
       <label title='[SELF]' style="font-weight:bold">
        [SELF]
       </label>
    {{else}}
      {{assign var=receiver value=$object->_ref_actor}}
      <a href="?m=eai&tab=vw_idx_interop_actors#interop_actor_guid={{$receiver->_guid}}">
        {{$receiver->_view}}
      </a>
    {{/if}}
  </td>
  <td>
    {{mb_value object=$object field="status"}}
  </td>
</tr>
