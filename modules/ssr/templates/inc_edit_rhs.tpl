{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
  * @package Mediboard
  * @subpackage ssr
  * @version $Revision: 7951 $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}

{{if !$rhs->_in_bounds}} 
<div class="small-warning">
  Le séjour ne comporte aucune journée dans la semaine de ce RHS.
  <br/>
  Ce RHS <strong>doit être supprimé</strong>.
</div>
{{/if}}

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
      <script type="text/javascript">

      Main.add( function(){
        var form = getForm("new-line-{{$rhs->_guid}}");
        CotationRHS.autocompleteCsARR(form);
        CotationRHS.autocompleteExecutant(form);
      } );

      </script>
      
      <form name="new-line-{{$rhs->_guid}}" action="?m={{$m}}" method="post" onsubmit="return CotationRHS.onSubmitLine(this);">

      {{mb_class class=CLigneActivitesRHS}}
      <input type="hidden" name="rhs_id" value="{{$rhs->_id}}" />

      <table class="form">
        <tr>
          <th class="title" colspan="2">Ajouter une ligne d'activité</th>
        </tr>
        <tr>
          <th>{{mb_label object=$rhs_line field=code_activite_csarr}}</th>
          <td>
            {{mb_field object=$rhs_line field=code_activite_csarr class="autocomplete"}}
            <div style="display: none;" class="autocomplete activite" id="{{$rhs->_guid}}_csarr_auto_complete"></div>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$rhs_line field=executant_id}}</th>
          <td>
            {{mb_field object=$rhs_line field=executant_id hidden=true}}
            {{mb_field object=$rhs_line field=code_intervenant_cdarr hidden=true}}
            {{mb_field object=$rhs_line field=_executant class="autocomplete"}}
            <div style="display:none;" class="autocomplete executant" id="{{$rhs->_guid}}_executant_auto_complete"></div>
          </td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="new" type="submit">
              {{tr}}CLigneActivitesRHS-title-create{{/tr}}
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