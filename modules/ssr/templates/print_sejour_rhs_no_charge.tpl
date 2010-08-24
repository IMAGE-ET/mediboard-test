{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
  * @package Mediboard
  * @subpackage ssr
  * @version $Revision: 7951 $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}

<script type="text/javascript">
//Main.add(window.print);
</script>

<!-- Fermeture des tableaux -->
    </td>
  </tr>
</table>

{{assign var=days value="CRHS"|static:days}}

{{foreach from=$sejours_rhs item=_rhs}}
{{assign var=sejour value=$_rhs->_ref_sejour}}
<table class="tbl">
  <tr>
    <th class="title" colspan="11" style="cursor: pointer"><a href="#" onclick="window.print()">{{$sejour}}</a></th>
  </tr>

  {{assign var=dependance value=$_rhs->_ref_dependances}}
  <tr>
    <th class="title" colspan="11">{{tr}}CDependancesRHS{{/tr}}</th>
  </tr>
  <tr>
    <th class="category">Categorie</th>
    <th>{{mb_label object=$dependance field=habillage}}</th>
    <th>{{mb_label object=$dependance field=deplacement}}</th>
    <th>{{mb_label object=$dependance field=alimentation}}</th>
    <th>{{mb_label object=$dependance field=continence}}</th>
    <th>{{mb_label object=$dependance field=comportement}}</th>
    <th>{{mb_label object=$dependance field=relation}}</th>
  </tr>
  
  <tr>
    <th class="category">Degré</th>
    <td>{{mb_value object=$dependance field=habillage}}</td>
    <td>{{mb_value object=$dependance field=deplacement}}</td>
    <td>{{mb_value object=$dependance field=alimentation}}</td>
    <td>{{mb_value object=$dependance field=continence}}</td>
    <td>{{mb_value object=$dependance field=comportement}}</td>
    <td>{{mb_value object=$dependance field=relation}}</td>
  </tr>
</table>

 <table class="tbl"> 
  <tr>
    <th class="title" colspan="11">Totaux RHS</th>
  </tr>
  {{foreach from=$types_activite item=_type name=liste_types}}
    {{assign var=code value=$_type->code}}
    {{assign var=rhs_id value=$_rhs->_id}}
    {{assign var=total value=$totaux.$rhs_id.$code}}
    {{if $smarty.foreach.liste_types.index % 3 == 0}}
    <tr>
    {{/if}}
      <td class="button">
        {{if $total}}{{$total}}{{else}}-{{/if}}
      </td>
      <th style="text-align: left">{{$_type->_shortview}}</th>
      
    {{if $smarty.foreach.liste_types.index % 3 == 3}}
    </tr>
    {{/if}}
  {{/foreach}}
</table>

 <table class="tbl">   
  <tr>
    <th class="title" colspan="11">{{tr}}CLigneActivitesRHS{{/tr}}</th>
  </tr>
  
  <tr>
    <th>{{mb_title object=$rhs_line field=code_activite_cdarr}}</th>
    <th>{{mb_title object=$rhs_line field=executant_id}}</th>
    {{foreach from=$days key=day item=litteral_day}}
    <th style="width: 1%">{{mb_title object=$rhs_line field=qty_$litteral_day}}</th>
    {{/foreach}}
  </tr>

    {{foreach from=$_rhs->_back.lines item=_line name=backlines}}
      {{assign var=executant value=$_line->_fwd.executant_id}}
      {{assign var=activite  value=$_line->_ref_code_activite_cdarr}}
      {{assign var=numsemaine value=$_rhs->_week_number}}
      {{assign var=indexforeach value=$smarty.foreach.backlines.index}}
            
      {{if $smarty.foreach.backlines.first}}
      
      {{/if}}
      <tr>
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
          {{mb_include template="inc_line_rhs" rhs=$_rhs}}
        {{/foreach}}
      </tr>
    {{foreachelse}}
    <tr>
      <td colspan="10"><em>{{tr}}CRHS-back-lines.empty{{/tr}}</em></td>
    </tr>
    {{/foreach}}
</table>

<br style="page-break-after: always;" />

{{foreachelse}}
<div class="small-info">Aucun RHS de sélectionné</div>
{{/foreach}}

<!-- Re-ouverture des tableaux -->
<table>
  <tr>
    <td>