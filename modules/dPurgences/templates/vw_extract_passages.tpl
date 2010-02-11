{{* $Id: vw_extract_passages.tpl 7641 2009-12-17 10:50:38Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7671 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module="dPcabinet" script="file"}}

<script type="text/javascript">
  
function changePage(page) {
  $V(getForm('listFilter').page,page);
}

</script>

<div>
  <strong>Total des RPUs extrait : {{$total_rpus}}</strong> 
</div>

<form name="listFilter" action="?m={{$m}}" method="get">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  <input type="hidden" name="page" value="{{$page}}" onchange="this.form.submit()"/>
        
  {{if $total_passages != 0}}
    {{mb_include module=system template=inc_pagination total=$total_passages current=$page change_page='changePage'}}
  {{/if}}
</form>
      
<table class="tbl">
  <tr>
    <th class="title" colspan="17">PASSAGES</th>
  </tr>
  <tr>
    <th>{{mb_title object=$extractPassages field="extract_passages_id"}}</th>
    <th>{{mb_title object=$extractPassages field="date_extract"}}</th>
    <th>{{mb_title object=$extractPassages field="debut_selection"}}</th>
    <th>{{mb_title object=$extractPassages field="fin_selection"}}</th>
    <th>{{mb_title object=$extractPassages field="_nb_rpus"}}</th>
    <th>{{mb_title object=$extractPassages field="date_echange"}}</th>
    <th>{{mb_title object=$extractPassages field="nb_tentatives"}}</th>
    <th>{{mb_title object=$extractPassages field="message_valide"}}</th>
    <th>Fichiers</th>
  </tr>
  {{foreach from=$listPassages item=_passage}}
  <tr>
    <td style="width:0.1%">
      {{$_passage->_id|str_pad:6:'0':$smarty.const.STR_PAD_LEFT}}
    </td>
    <td style="width:0.1%">
      <label title='{{mb_value object=$_passage field="date_extract"}}'>
        {{mb_value object=$_passage field="date_extract" format=relative}}
      </label>
    </td>
    <td style="width:0.1%">
      {{mb_value object=$_passage field="debut_selection"}}
    </td>
    <td style="width:0.1%">
      {{mb_value object=$_passage field="fin_selection"}}
    </td>
    <td style="width:0.1%">
      {{mb_value object=$_passage field="_nb_rpus"}}
    </td>
    <td style="width:0.1%">
      <label title='{{mb_value object=$_passage field="date_echange"}}'>
        {{mb_value object=$_passage field="date_echange" format=relative}}
      </label>
    </td>
    <td style="width:0.1%" class="{{if $_passage->nb_tentatives > 5}}warning{{/if}}">
      {{mb_value object=$_passage field="nb_tentatives"}}
    </td>
    <td style="width:0.1%" class="{{if !$_passage->message_valide}}error{{/if}}">
      {{mb_value object=$_passage field="message_valide"}}
    </td>
    <td id="files-{{$_passage->_class_name}}-{{$_passage->_id}}">
      <table class="tbl">
        {{foreach from=$_passage->_ref_files item=_file}}
        <tr>
          <td>
            <a href="#" class="action" 
               onclick="File.popup('{{$_passage->_class_name}}','{{$_passage->_id}}','{{$_file->_class_name}}','{{$_file->_id}}');"
               onmouseover="ObjectTooltip.createEx(this, '{{$_file->_guid}}', 'objectViewHistory')">
              {{$_file->file_name}}
            </a>
            <small>({{$_file->_file_size}})</small>
          </td>
        </tr>
        {{foreachelse}}
        <tr>
          <td>
            <em>
              {{tr}}{{$_passage->_class_name}}{{/tr}} :
              {{tr}}CFile.none{{/tr}}
            </em>
          </td>
        </tr>
        {{/foreach}}
      </table>       
    </td>
  </tr>
  {{/foreach}}
</table>