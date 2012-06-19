{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 10391 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="Edit-{{$sender_source->_guid}}" action="?m={{$m}}" method="post" onsubmit="return ViewSenderSource.onSubmit(this);">
  {{mb_class object=$sender_source}}
  {{mb_key   object=$sender_source}}
  <input type="hidden" name="del" value="0" />
  
  <table class="form">
    {{mb_include template=inc_form_table_header object=$sender_source}}
    
    <tr>
      <th>{{mb_label object=$sender_source field=name}}</th>
      <td>{{mb_field object=$sender_source field=name}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$sender_source field=libelle}}</th>
      <td>{{mb_field object=$sender_source field=libelle}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$sender_source field=group_id}}</th>
      <td>{{mb_field object=$sender_source field=group_id form="Edit-`$sender_source->_guid`" autocomplete="true,1,50,true,true"}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$sender_source field=actif}}</th>
      <td>{{mb_field object=$sender_source field=actif}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$sender_source field=archive}}</th>
      <td>{{mb_field object=$sender_source field=archive}}</td>
    </tr>
    
    <tr>
      <td class="button" colspan="2">
        {{if $sender_source->_id}}
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
        <button class="trash" type="button" onclick="ViewSenderSource.confirmDeletion(this.form);">
          {{tr}}Delete{{/tr}}
        </button>
        {{else}}
        <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>

{{if $sender_source->_id}}
  {{assign var=sender_source_ftp value=$sender_source->_ref_source_ftp}}
   
  <script type="text/javascript">
    Main.add(function () {
      Control.Tabs.create('tabs-{{$sender_source->_guid}}', true);
    });
  </script>
    
  <table class="form">
    <tr>
      <td> 
        {{mb_include module=system template=inc_config_exchange_source source=$sender_source_ftp}}
      </td>
    </tr>
  </table>
{{/if}}