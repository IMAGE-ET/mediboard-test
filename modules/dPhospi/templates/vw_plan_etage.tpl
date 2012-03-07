{{* $Id: vw_plan_etage.tpl $ *}}

{{*
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 11962 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}
{{mb_script module=hospi script=drag_chambre}}

<style type="text/css">
.chambre{
  height:100%;
  background-color: #EEE;
}

div#list-chambres-non-placees{
  width:100px;
  min-height:720px;
}

div#grille{
  float:left;
  margin-left:5px;
  margin-right:5px; 
  width:100%;
}

#grille td.conteneur-chambre{
  width:75px;
  height:50px;
  text-align:center;
  background-color:white;
}
</style>

<table class="main">
  <tr>
    <th class="title">Chambres  </th>
    <th class="title">{{if $service_id!=""}}{{$service_selectionne->nom}}{{else}}Plan{{/if}}</th>
  </tr>
  <tr>
    <td>
      
      <form action="?m=dPhospi&tab=vw_plan_etage" method="post">
          <select name="service_id" onchange="this.form.submit()">
            <option value="">&mdash; Service</option>
            {{foreach from=$services item=_service}}    
              <option value="{{$_service->_ref_service->_id}}" {{if $service_id!="" && $service_id==$_service->_ref_service->_id}}selected="selected"{{/if}}>{{ $_service->_ref_service->nom}}</option>
            {{/foreach}}
          </select>
      </form>
      
      <div id="list-chambres-non-placees" >
      {{foreach from=$chambres_non_placees item=_chambre}}
          <div data-chambre-id="{{$_chambre->_id}}" class="chambre">{{$_chambre->nom}}</div>
      {{/foreach}}
      </div>
    </td>
    <td> 
      <div id="grille" >
        <table class="main tbl">
        {{foreach from=$grille item=ligne key=y }}
         <tr style="height:35px;min-width:750px;">
          {{foreach from=$ligne item=_zone key=x}}
          
            <td data-x="{{$x}}" data-y="{{$y}}" class="conteneur-chambre" >
              {{if $_zone!='0'}}
               <div data-chambre-id="{{$_zone->chambre_id}}" class="chambre">{{$_zone}}</div>
              {{/if}}
            </td>
                       
         {{/foreach}}
        </tr>
        {{/foreach}}
        </tr>
        </table>
      </div>
    </td>
  </tr>
</table>