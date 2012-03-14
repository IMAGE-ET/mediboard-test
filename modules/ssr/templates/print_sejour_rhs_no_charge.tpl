{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
  * @package Mediboard
  * @subpackage ssr
  * @version $Revision: 7951 $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}

{{mb_include style=$style template=open_printable}}

<div class="not-printable">
  <button type="button" class="print not-printable" onclick="window.print()">
    {{tr}}Print{{/tr}}
    {{$sejours_rhs|@count}} {{tr}}CRHS{{/tr}}
  </button>
</div>

{{assign var=days value="CRHS"|static:days}}

{{foreach from=$sejours_rhs item=_rhs}}
{{assign var=sejour value=$_rhs->_ref_sejour}}
<table class="tbl">
  <tr>
    <th class="title" colspan="11">
      <big>
        {{$sejour}}<br/>
        {{tr}}CRHS{{/tr}} {{$_rhs}}
        &mdash;
        {{mb_include module=system template=inc_interval_date from=$_rhs->date_monday to=$_rhs->_date_sunday}}
      </big>
    </th>
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
  {{assign var=totaux value=$_rhs->_totaux}}
  {{foreach from=$_rhs->_ref_types_activite item=_type name=liste_types}}
    {{assign var=code value=$_type->code}}
    {{assign var=total value=$totaux.$code}}
    {{if $smarty.foreach.liste_types.index % 3 == 0}}
    <tr>
    {{/if}}
      <td class="button">
        {{if $total}}{{$total|default:'-'}}{{else}}-{{/if}}
      </td>
      <th style="text-align: left">{{$_type->_shortview}}</th>
      
    {{if $smarty.foreach.liste_types.index % 3 == 3}}
    </tr>
    {{/if}}
  {{/foreach}}
</table>

{{mb_include template=inc_lines_rhs rhs=$_rhs}}

<br style="page-break-after: always;" />

{{foreachelse}}
<div class="small-info">{{tr}}CRHS-none{{/tr}}</div>
{{/foreach}}

{{mb_include style=$style template=close_printable}}
