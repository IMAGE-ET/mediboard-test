{{foreach from=$transformations item=_transformation}}
  {{assign var=eai_transformation_rule value=$_transformation->_ref_eai_transformation_rule}}

  <tr {{if !$_transformation->active}}class="opacity-30"{{/if}}>
    <td class="button">
      <button class="button edit notext compact"
              onclick="EAITransformation.edit('{{$_transformation->_id}}', '{{$message_name}}', '{{$event_name}}');"
              title="{{tr}}Edit{{/tr}}">
        {{tr}}Edit{{/tr}}
      </button>
    </td>
    <td>{{mb_value object=$_transformation field=eai_transformation_id}}</td>
    <td>
      <span onmouseover="ObjectTooltip.createEx(this, '{{$eai_transformation_rule->_guid}}');">
         #{{$eai_transformation_rule->_id}}
       </span>
    </td>
    <td class="text compact">{{mb_value object=$_transformation field=standard}}</td>
    <td class="text compact">{{mb_value object=$_transformation field=domain}}</td>
    <td class="text compact">{{mb_value object=$_transformation field=profil}}</td>
    <td class="text compact">{{mb_value object=$_transformation field=message}}</td>
    <td class="text compact">{{mb_value object=$_transformation field=transaction}}</td>
    <td>{{mb_value object=$_transformation field=version}}</td>
    <td>{{mb_value object=$_transformation field=extension}}</td>
    <td>{{mb_value object=$_transformation field=active}}</td>
    <td class="text compact">
      {{mb_value object=$_transformation field="rank"}}
      <!-- Order -->
      <form name="formOrderTranformation-{{$_transformation->_id}}" method="post"
            onsubmit="return onSubmitFormAjax(this, EAITransformation.refreshList.curry('{{$event_name}}', '{{$actor->_guid}}'))">

        <input type="hidden" name="dosql" value="do_manage_transformation" />
        <input type="hidden" name="m" value="eai" />
        <input type="hidden" name="ajax" value="1" />
        <input type="hidden" name="transformation_id_move" value="{{$_transformation->_id}}" />
        <input type="hidden" name="direction" value="" />

        <img src="./images/icons/updown.gif" usemap="#map-{{$_transformation->_id}}" />
        <map name="map-{{$_transformation->_id}}">
          <area coords="0,0,10,7"  href="#1" onclick="$V(this.up('form').direction, 'up');   this.up('form').onsubmit();" />
          <area coords="0,8,10,14" href="#1" onclick="$V(this.up('form').direction, 'down'); this.up('form').onsubmit();" />
        </map>
      </form>
    </td>
  </tr>
  {{foreachelse}}
  <tr><td class="empty" colspan="14">{{tr}}CEAITransformation.none{{/tr}}</td></tr>
{{/foreach}}