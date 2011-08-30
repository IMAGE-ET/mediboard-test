{{* $Id: vw_plan_etage.tpl $ *}}

{{*
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 11962 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}
{{mb_script module=dPhospi script=drag_chambre}}
<table class="main">
  <tr>
    <th class="title">Chambres  </th>
    <th class="title">Plan    </th>
  </tr>
  <tr>
    <td>
      
      <div id="divGauche" style="width:100px;min-height:720px;clear:both;float:left;">
        
          <form action="?m=dPhospi&tab=vw_plan_etage" method="post">
              <select name="service_id" onchange="this.form.submit()">
                <option value="">&mdash; Service</option>
                {{foreach from=$services item=_service}}    
                  <option value="{{$_service->_ref_service->_id}}" {{if $service_id!="" && $service_id==$_service->_ref_service->_id}}selected="selected"{{/if}}>{{ $_service->_ref_service->nom}}</option>
                {{/foreach}}
              </select>
          </form>
          <table class="main tbl">
          {{foreach from=$chambres item=_chambre}}
            {{if !$_chambre->plan}} 
              <tr style="border:1px blue dotted; width:100%;height:25px; text-align:center;padding-top:10px;" >
                <td style="width:75px;height:35px;text-align:center;background-color:white;">
								 <div id="{{$_chambre->_id}}" style="height:100%;background-color: #EEE;">{{ $_chambre->nom}}</div>
								</td>
              </tr>
            {{/if}}
          {{/foreach}}
          </table>
      </div>
      
    </td>
    <td> 
      <div id="divDroite" style="float:left;margin-left:5px;margin-right:5px;">
        <table class="main tbl">
        {{foreach from=$zones item=_zone }}
          {{if $_zone%10==0}}
            {{if $_zone==0}}
            <tr style="height:35px;min-width:750px;">
              {{else}}
              </tr>
              <tr style="height:35px;min-width:750px;">
            {{/if}}
          {{/if}}
          {{if $les_chambres[$_zone]!='null'}}
              <td id="zone{{$_zone}}" style="width:75px;height:35px;text-align:center;background-color:white;" >
                <div id="{{$les_chambres[$_zone]->_id}}"  style="height:100%;background-color: #EEE;" >{{$les_chambres[$_zone]}}</div>
              </td>
            {{else}}
            <td style="width:75px;height:35px;text-align:center;background-color:white;" id="zone{{$_zone}}">
            
            </td>
          {{/if}} 
            
        {{/foreach}}
        </tr>
        </table>
      </div>
      <div id="id" style="visibility:hidden;"></div>
    </td>
  </tr>
</table>