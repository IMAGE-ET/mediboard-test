{{* $Id: inc_main_courante.tpl 7114 2009-10-22 15:50:59Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision: 7114 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

Main.add(function () {
  var tabs = Control.Tabs.create('tabs-prat');
});
</script>

<table class="main">
  <tr>
    <td style="width:1%; white-space:nowrap;">
      <ul id="tabs-prat" class="control_tabs_vertical">
        {{foreach from=$sejour_no_prat key=_responsable_id item=_sejours}}
          {{assign var=_sejour value=$_sejours.0 }}
          <li><a href="#prat-{{$_responsable_id}}">{{$_sejour->_ref_praticien}} ({{$_sejours|@count}})</a></li>
        {{/foreach}}
          <li>
            <form name="repairSejour" action="?m={{$m}}&amp;a=vw_resp_no_prat&amp;dialog=1" method="post">
              <input type="hidden" name="repair" value="1" />
              <br />
              <button type="button" class="tick" onclick="this.form.submit()">Corriger tous les séjours valide</button>
            </form>
          </li>
      </ul>
    </td>
    <td>
      {{foreach from=$sejour_no_prat key=_responsable_id item=_sejours}}
        <table class="tbl" id="prat-{{$_responsable_id}}" style="display: none;">
        {{foreach from=$_sejours item=_sejour}}
        <tr>
          <td>
            <div class="{{if ($_sejour->_ref_consult_atu->_ref_chir->_id)}}message{{else}}error{{/if}}">
              <span onclick="window.opener.location.href='?m=dPplanningOp&tab=vw_edit_sejour&sejour_id={{$_sejour->_id}}'" onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}')">
              [{{$_sejour->_num_dossier}}] - {{$_sejour}} ({{$_sejour->_ref_consult_atu->_ref_chir}})
              </span>
            </div>
          </td>
        </tr>
        {{/foreach}}
        </table>
      {{/foreach}}
    </td>
  </tr>
</table>