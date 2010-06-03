{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
  * @package Mediboard
  * @subpackage ssr
  * @version $Revision: 7951 $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}

{{if !$rhs->_id}}
<form name="Edit-CRHS-{{$rhs->_date_sunday}}" action="?m={{$m}}" method="post" onsubmit="return CotationRHS.onSubmitRHS(this)">

<input type="hidden" name="m" value="ssr" />
<input type="hidden" name="dosql" value="do_rhs_aed" />
<input type="hidden" name="del" value="0" />

{{mb_key object=$rhs}}
{{mb_field object=$rhs field=sejour_id  hidden=1}}

<table class="form">
  <tr>
    <th>{{mb_label object=$rhs field=date_monday}}</th>
    <td>{{mb_field object=$rhs field=date_monday readonly=1}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$rhs field=_date_sunday}}</th>
    <td>{{mb_field object=$rhs field=_date_sunday readonly=1}}</td>
  </tr>
  <tr>
    <td class="button" colspan="4">
      <button class="new" type="submit">
        {{tr}}CRHS-title-create{{/tr}}
      </button>
    </td>
  </tr>
</table>

</form>
{{/if}}


{{if $rhs->_id}}

{{if !$rhs->_in_bounds}} 
<div class="small-warning">
	Le séjour ne comporte aucune journée dans la semaine de ce RHS.
	<br/>
	Ce RHS <strong>doit être supprimé</strong>.
</div>
{{/if}}

<script type="text/javascript">

Main.add( function(){
	var url = new Url("ssr", "httpreq_do_intervenant_autocomplete");
	
	url.autoComplete("new-line-{{$rhs->_guid}}__executant", "{{$rhs->_guid}}_executant_auto_complete", {
    dropdown: true,
    minChars: 2,
    updateElement: function(element) { CotationRHS.updateExecutant(element, getForm("new-line-{{$rhs->_guid}}")); }
	} );
	var url = new Url("ssr", "httpreq_do_activite_autocomplete");
	
	url.autoComplete("new-line-{{$rhs->_guid}}_code_activite_cdarr", "{{$rhs->_guid}}_activite_auto_complete", {
    dropdown: false,
    minChars: 2,
    updateElement: function(element) { CotationRHS.updateActivite(element, getForm("new-line-{{$rhs->_guid}}")); }
	} );
} );

</script>

<table class="main">
  <tr>
    <td rowspan="2">
      {{mb_include module="ssr" template="inc_dependances_rhs"}}
    </td>
    <td class="greedyPane" id="totaux-{{$rhs->_id}}">
      {{mb_include module="ssr" template="inc_totaux_rhs"}}
    </td>
  </tr>
  <tr>
    <td>
      <form name="new-line-{{$rhs->_guid}}" action="?m={{$m}}" method="post" onsubmit="return CotationRHS.onSubmitLine(this);">
      <input type="hidden" name="m" value="ssr" />
      <input type="hidden" name="dosql" value="do_line_rhs_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="rhs_id" value="{{$rhs->_id}}" />
      <table class="form">
        <tr>
          <th class="title" colspan="2">Ajouter une ligne d'activité</th>
        </tr>
        <tr>
          <th>{{mb_label object=$rhs_line field=code_activite_cdarr}}</th>
          <td>
            {{mb_field object=$rhs_line field=code_activite_cdarr class="autocomplete"}}
            <div style="display:none;" class="autocomplete" id="{{$rhs->_guid}}_activite_auto_complete"></div>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$rhs_line field=executant_id}}</th>
          <td>
            {{mb_field object=$rhs_line field=executant_id hidden=true}}
            {{mb_field object=$rhs_line field=code_intervenant_cdarr hidden=true}}
            {{mb_field object=$rhs_line field=_executant class="autocomplete"}}
            <div style="display:none;" class="autocomplete" id="{{$rhs->_guid}}_executant_auto_complete"></div>
          </td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="new" type="submit">
              {{tr}}CLigneActivitesRHS-title-create{{/tr}}
            </button>
            <button class="change" type="button" onclick="CotationRHS.recalculatehRHS('{{$rhs->_id}}')">
              {{tr}}CLigneActivitesRHS.recalculate{{/tr}}
            </button>
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>

<table class="tbl">
  <tr>
    <th style="width: 1%"></th>
    <th>{{mb_title object=$rhs_line field=code_activite_cdarr}}</th>
    <th>{{mb_title object=$rhs_line field=executant_id}}</th>
    <th style="width: 1%" {{if !$rhs->_in_bounds_mon}}class="disabled"{{/if}}>{{mb_title object=$rhs_line field=qty_mon}}</th>
    <th style="width: 1%" {{if !$rhs->_in_bounds_tue}}class="disabled"{{/if}}>{{mb_title object=$rhs_line field=qty_tue}}</th>
    <th style="width: 1%" {{if !$rhs->_in_bounds_wed}}class="disabled"{{/if}}>{{mb_title object=$rhs_line field=qty_wed}}</th>
    <th style="width: 1%" {{if !$rhs->_in_bounds_thu}}class="disabled"{{/if}}>{{mb_title object=$rhs_line field=qty_thu}}</th>
    <th style="width: 1%" {{if !$rhs->_in_bounds_fri}}class="disabled"{{/if}}>{{mb_title object=$rhs_line field=qty_fri}}</th>
    <th style="width: 1%" {{if !$rhs->_in_bounds_sat}}class="disabled"{{/if}}>{{mb_title object=$rhs_line field=qty_sat}}</th>
    <th style="width: 1%" {{if !$rhs->_in_bounds_sun}}class="disabled"{{/if}}>{{mb_title object=$rhs_line field=qty_sun}}</th>
  </tr>
  {{foreach from=$rhs->_back.lines item=_line name=backlines}}
  {{assign var=executant value=$_line->_fwd.executant_id}}
  {{assign var=activite  value=$_line->_ref_code_activite_cdarr}}
  {{assign var=numsemaine value=$rhs->_week_number}}
  {{assign var=indexforeach value=$smarty.foreach.backlines.index}}
  <tr>
    <td>
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
    <td class="button {{if !$rhs->_in_bounds_mon}}disabled{{elseif $_line->qty_mon}}ok{{/if}}">
      {{if $rhs->_in_bounds_mon}}
      <form name="chg-mon-{{$_line->_guid}}" action="?m={{$m}}" method="post" onsubmit="return CotationRHS.onSubmitQuantity(this, 'qty_mon');">
      <input type="hidden" name="m" value="ssr" />
      <input type="hidden" name="dosql" value="do_line_rhs_aed" />
      <input type="hidden" name="del" value="0" />
      {{mb_key object=$_line}}
      <input type="hidden" name="rhs_id" value="{{$rhs->_id}}" />
      {{assign var=line_guid value=$_line->_guid}}
      {{assign var=qty_form value="chg-mon-$line_guid"}}
      {{assign var=day value=1}}
      {{mb_field object=$_line field=qty_mon form=$qty_form onchange="this.form.onsubmit()" tabindex="$numsemaine$indexforeach$day"}}
      </form>
      {{else}}
      {{$_line->qty_mon}}
      {{/if}}
    </td>
    <td class="button {{if !$rhs->_in_bounds_tue}}disabled{{elseif $_line->qty_tue}}ok{{/if}}">
      {{if $rhs->_in_bounds_tue}}
      <form name="chg-tue-{{$_line->_guid}}" action="?m={{$m}}" method="post" onsubmit="return CotationRHS.onSubmitQuantity(this, 'qty_tue');">
      <input type="hidden" name="m" value="ssr" />
      <input type="hidden" name="dosql" value="do_line_rhs_aed" />
      <input type="hidden" name="del" value="0" />
      {{mb_key object=$_line}}
      <input type="hidden" name="rhs_id" value="{{$rhs->_id}}" />
      {{assign var=line_guid value=$_line->_guid}}
      {{assign var=qty_form value="chg-tue-$line_guid"}}
      {{assign var=day value=2}}
      {{mb_field object=$_line field=qty_tue form=$qty_form onchange="this.form.onsubmit()" tabindex="$numsemaine$indexforeach$day"}}
      </form>
      {{else}}
      {{$_line->qty_tue}}
      {{/if}}
    </td>
    <td class="button {{if !$rhs->_in_bounds_wed}}disabled{{elseif $_line->qty_wed}}ok{{/if}}">
      {{if $rhs->_in_bounds_wed}}
      <form name="chg-wed-{{$_line->_guid}}" action="?m={{$m}}" method="post" onsubmit="return CotationRHS.onSubmitQuantity(this, 'qty_wed');">
      <input type="hidden" name="m" value="ssr" />
      <input type="hidden" name="dosql" value="do_line_rhs_aed" />
      <input type="hidden" name="del" value="0" />
      {{mb_key object=$_line}}
      <input type="hidden" name="rhs_id" value="{{$rhs->_id}}" />
      {{assign var=line_guid value=$_line->_guid}}
      {{assign var=qty_form value="chg-wed-$line_guid"}}
      {{assign var=day value=3}}
      {{mb_field object=$_line field=qty_wed form=$qty_form onchange="this.form.onsubmit()" tabindex="$numsemaine$indexforeach$day"}}
      </form>
      {{else}}
      {{$_line->qty_wed}}
      {{/if}}
    </td>
    <td class="button {{if !$rhs->_in_bounds_thu}}disabled{{elseif $_line->qty_thu}}ok{{/if}}">
      {{if $rhs->_in_bounds_thu}}
      <form name="chg-thu-{{$_line->_guid}}" action="?m={{$m}}" method="post" onsubmit="return CotationRHS.onSubmitQuantity(this, 'qty_thu');">
      <input type="hidden" name="m" value="ssr" />
      <input type="hidden" name="dosql" value="do_line_rhs_aed" />
      <input type="hidden" name="del" value="0" />
      {{mb_key object=$_line}}
      <input type="hidden" name="rhs_id" value="{{$rhs->_id}}" />
      {{assign var=line_guid value=$_line->_guid}}
      {{assign var=qty_form value="chg-thu-$line_guid"}}
      {{assign var=day value=4}}
      {{mb_field object=$_line field=qty_thu form=$qty_form onchange="this.form.onsubmit()" tabindex="$numsemaine$indexforeach$day"}}
      </form>
      {{else}}
      {{$_line->qty_thu}}
      {{/if}}
    </td>
    <td class="button {{if !$rhs->_in_bounds_fri}}disabled{{elseif $_line->qty_fri}}ok{{/if}}">
      {{if $rhs->_in_bounds_fri}}
      <form name="chg-fri-{{$_line->_guid}}" action="?m={{$m}}" method="post" onsubmit="return CotationRHS.onSubmitQuantity(this, 'qty_fri');">
      <input type="hidden" name="m" value="ssr" />
      <input type="hidden" name="dosql" value="do_line_rhs_aed" />
      <input type="hidden" name="del" value="0" />
      {{mb_key object=$_line}}
      <input type="hidden" name="rhs_id" value="{{$rhs->_id}}" />
      {{assign var=line_guid value=$_line->_guid}}
      {{assign var=qty_form value="chg-fri-$line_guid"}}
      {{assign var=day value=5}}
      {{mb_field object=$_line field=qty_fri form=$qty_form onchange="this.form.onsubmit()" tabindex="$numsemaine$indexforeach$day"}}
      </form>
      {{else}}
      {{$_line->qty_fri}}
      {{/if}}
    </td>
    <td class="button {{if !$rhs->_in_bounds_sat}}disabled{{elseif $_line->qty_sat}}ok{{/if}}">
      {{if $rhs->_in_bounds_sat}}
      <form name="chg-sat-{{$_line->_guid}}" action="?m={{$m}}" method="post" onsubmit="return CotationRHS.onSubmitQuantity(this, 'qty_sat');">
      <input type="hidden" name="m" value="ssr" />
      <input type="hidden" name="dosql" value="do_line_rhs_aed" />
      <input type="hidden" name="del" value="0" />
      {{mb_key object=$_line}}
      <input type="hidden" name="rhs_id" value="{{$rhs->_id}}" />
      {{assign var=line_guid value=$_line->_guid}}
      {{assign var=qty_form value="chg-sat-$line_guid"}}
      {{assign var=day value=6}}
      {{mb_field object=$_line field=qty_sat form=$qty_form onchange="this.form.onsubmit()" tabindex="$numsemaine$indexforeach$day"}}
      </form>
      {{else}}
      {{$_line->qty_sat}}
      {{/if}}
    </td>
    <td class="button {{if !$rhs->_in_bounds_sun}}disabled{{elseif $_line->qty_sun}}ok{{/if}}">
      {{if $rhs->_in_bounds_sun}}
      <form name="chg-sun-{{$_line->_guid}}" action="?m={{$m}}" method="post" onsubmit="return CotationRHS.onSubmitQuantity(this, 'qty_sun');">
      <input type="hidden" name="m" value="ssr" />
      <input type="hidden" name="dosql" value="do_line_rhs_aed" />
      <input type="hidden" name="del" value="0" />
      {{mb_key object=$_line}}
      <input type="hidden" name="rhs_id" value="{{$rhs->_id}}" />
      {{assign var=line_guid value=$_line->_guid}}
      {{assign var=qty_form value="chg-sun-$line_guid"}}
      {{assign var=day value=7}}
      {{mb_field object=$_line field=qty_sun form=$qty_form onchange="this.form.onsubmit()" tabindex="$numsemaine$indexforeach$day"}}
      </form>
      {{else}}
      {{$_line->qty_sun}}
      {{/if}}
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10"><em>{{tr}}CRHS-back-lines.empty{{/tr}}</em></td>
  </tr>
  {{/foreach}}
</table>
{{/if}}