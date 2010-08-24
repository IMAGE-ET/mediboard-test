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
      {{if $rhs->facture == 1}}
        {{mb_include template="inc_dependances_rhs_charged"}}
      {{else}}
        {{mb_include template="inc_dependances_rhs"}}
      {{/if}}
    </td>
    <td class="greedyPane" id="totaux-{{$rhs->_id}}">
      {{mb_include template="inc_totaux_rhs"}}
    </td>
  </tr>
  
  <tr>
    <td>
      {{if $rhs->facture == 1}}
      <div class="small-warning">{{tr}}CRHS.charged{{/tr}}</div>
      {{else}}
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
    {{/if}}
  </tr>
</table>

{{mb_include template="inc_lines_rhs"}}
{{/if}}