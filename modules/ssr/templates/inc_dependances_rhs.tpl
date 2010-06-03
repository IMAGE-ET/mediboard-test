{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
  * @package Mediboard
  * @subpackage ssr
  * @version $Revision: 7951 $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *
}}
  
{{assign var=dependances value=$rhs->_ref_dependances}}
<form name="dependances-{{$rhs->_guid}}" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this);">
  <input type="hidden" name="m" value="ssr" />
  <input type="hidden" name="dosql" value="do_dependances_rhs_aed" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$dependances}}
  {{mb_field object=$dependances field=rhs_id hidden=true}}
  <table class="form">
    <tr>
      <th class="title" colspan="2">{{tr}}CDependancesRHS{{/tr}}</th>
    </tr>
    <tr>
      <th class="category">Categorie</th>
      <th class="category">Degré</th>
    </tr>
    <tr>
      <th>{{mb_label object=$dependances field=habillage}}</th>
      <td>{{mb_field object=$dependances field=habillage tabindex="10001" onchange="this.form.onsubmit()"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$dependances field=deplacement}}</th>
      <td>{{mb_field object=$dependances field=deplacement tabindex="10002" onchange="this.form.onsubmit()"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$dependances field=alimentation}}</th>
      <td>{{mb_field object=$dependances field=alimentation tabindex="10003" onchange="this.form.onsubmit()"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$dependances field=continence}}</th>
      <td>{{mb_field object=$dependances field=continence tabindex="10004" onchange="this.form.onsubmit()"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$dependances field=comportement}}</th>
      <td>{{mb_field object=$dependances field=comportement tabindex="10005" onchange="this.form.onsubmit()"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$dependances field=relation}}</th>
      <td>{{mb_field object=$dependances field=relation tabindex="10006" onchange="this.form.onsubmit()"}}</td>
    </tr>
  </table>
</form>  