{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
  * @package Mediboard
  * @subpackage ssr
  * @version $Revision: 7951 $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}

{{ if !@$read_only}}
  {{assign var=read_only value=false}}
{{/if}}

{{ if $rhs->facture == 1}}
  {{assign var=read_only value=true}}
{{/if}}
  
{{assign var=days value="CRHS"|static:days}}
  
<table class="tbl">
  <tr>
    <th style="width: 1%"></th>
    <th>{{mb_title object=$rhs_line field=code_activite_cdarr}}</th>
    <th>{{mb_title object=$rhs_line field=executant_id}}</th>
    
    {{foreach from=$days key=day item=litteral_day}}
    <th class="category" style="width: 1%">{{mb_title object=$rhs_line field=qty_$litteral_day}}</th>
    {{/foreach}}
  </tr>
  {{foreach from=$rhs->_back.lines item=_line name=backlines}}
    {{assign var=executant value=$_line->_fwd.executant_id}}
    {{assign var=activite  value=$_line->_ref_code_activite_cdarr}}
    {{assign var=numsemaine value=$rhs->_week_number}}
    {{assign var=indexforeach value=$smarty.foreach.backlines.index}}
    
    <tr>
      <td>
        {{ if !$read_only}}
        <form name="del-line-{{$_line->_guid}}" action="?m={{$m}}" method="post" onsubmit="return CotationRHS.onSubmitLine(this);">
          <input type="hidden" name="m" value="ssr" />
          <input type="hidden" name="dosql" value="do_line_rhs_aed" />
          <input type="hidden" name="del" value="1" />
          {{mb_key object=$_line}}
          <input type="hidden" name="rhs_id" value="{{$rhs->_id}}" />
          <button class="notext trash" type="button" onclick="confirmDeletion(this.form, {
            typeName:'l\'activité',
            objName:'{{$_line->_view|smarty:nodefaults|JSAttribute}}',
            ajax: 1})">
            {{tr}}Delete{{/tr}}
          </button>
        </form>
        {{/if}}
      </td>
      <td class="text">
        {{$activite->_view}}
        <br />
        <small>{{$activite->_ref_type_activite->_view}}</small>
      </td>
      <td class="text">
        {{mb_include module="mediusers" template="inc_vw_mediuser" mediuser=$executant}}
        <br />
        <small>{{$_line->_ref_code_intervenant_cdarr->_view}}</small>
      </td>
      
      {{foreach from=$days key=day item=litteral_day}}
        {{mb_include template="inc_line_rhs"}}
      {{/foreach}}
    </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10"><em>{{tr}}CRHS-back-lines.empty{{/tr}}</em></td>
  </tr>
  {{/foreach}}
</table>