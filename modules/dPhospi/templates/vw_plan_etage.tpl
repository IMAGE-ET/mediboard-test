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
  td.conteneur_chambres_non_places{
    width:150px;
  }
  table#table_grille {
    table-layout:fixed; 
  }
  #grille td.conteneur-chambre{
    background-color:white;
    height:60px;
  } 
  .chambre{
    text-align:center;
    height:60px;
    padding: 1px;
    box-shadow: 0 0 0 1px silver;
  }
  .chambre a{
    text-shadow:  0 0 0 transparent,
                -1px  0  .0px rgba(255,255,255,.7), 
                 0   1px .0px rgba(255,255,255,.7), 
                 1px  0  .0px rgba(255,255,255,.7), 
                 0  -1px .0px rgba(255,255,255,.7);
  }
  #list-chambres-non-placees{
    min-height:200px;
  }
  #list-chambres-non-placees div{
    height: 30px;
    width: 100px;
    margin-top: 2px;
    background-color: #DDDDDD;
  }
</style>

<table class="main" style="text-align:center;">
  <tr>
    <th class="title">Chambres{{* non placées*}}</th>
    <th class="title">{{if $service_id!=""}}Plan du service '{{$service_selectionne->nom}}'{{else}}Plan{{/if}}</th>
  </tr>
  <tr id="rechargement_grille">
    <td class="conteneur_chambres_non_places">
      <form action="?m=dPhospi&tab=vw_plan_etage" method="post">
          <select name="service_id" onchange="this.form.submit()">
            <option value="">&mdash; Service</option>
            {{foreach from=$services item=_service}}    
              <option value="{{$_service->_id}}" {{if $service_id!="" && $service_id==$_service->_id}}selected="selected"{{/if}}>{{ $_service->nom}}</option>
            {{/foreach}}
          </select>
      </form><br/>
      
      <div id="list-chambres-non-placees" >
      {{foreach from=$chambres_non_placees item=_chambre}}
        <div data-chambre-id="{{$_chambre->_id}}" class="chambre draggable" 
            {{if $_chambre->_ref_emplacement}}
              (data-largeur-nb="{{$_chambre->_ref_emplacement->largeur}}"
              data-hauteur-nb="{{$_chambre->_ref_emplacement->hauteur}}"  style="background-color:#{{$_chambre->_ref_emplacement->color}};"
            {{/if}}
          >
          <form name="Emplacement-{{$_chambre->_id}}" action="" method="post">
            <input type="hidden" name="dosql" value="do_emplacement_aed" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="m" value="dPhospi" />  
            <input type="hidden" name="chambre_id" value="{{$_chambre->_id}}" />   
            <input type="hidden" name="plan_x"  value="" />
            <input type="hidden" name="plan_y"  value="" />
          </form>
          {{$_chambre->nom}}
        </div>
      {{/foreach}}
      </div>
    </td>
    <td> 
      <div id="grille" >
        <table class="main tbl" id="table_grille">
        {{foreach from=$grille item=ligne key=y }}
         <tr>
          {{foreach from=$ligne item=_zone key=x}}
            {{if $_zone!='0'}}
              <td data-x="{{$x}}" data-y="{{$y}}" class="conteneur-chambre draggable" rowspan="{{$_zone->_ref_emplacement->hauteur}}" colspan="{{$_zone->_ref_emplacement->largeur}}" >
                <div data-chambre-id="{{$_zone->chambre_id}}" class="chambre" data-largeur-nb="{{$_zone->_ref_emplacement->largeur}}"
                  data-hauteur-nb="{{$_zone->_ref_emplacement->hauteur}}" style="background-color:#{{$_zone->_ref_emplacement->color}};height:{{$_zone->_ref_emplacement->hauteur*60}}px">
                 
                  <form name="Emplacement-{{$_zone->_id}}" action="" method="post">
                    <input type="hidden" name="dosql" value="do_emplacement_aed" />
                    <input type="hidden" name="del" value="0" />
                    <input type="hidden" name="m" value="dPhospi" />  
                    <input type="hidden" name="chambre_id" value="{{$_zone->chambre_id}}" />                        
                    {{mb_key object=$_zone->_ref_emplacement}}
                    <input type="hidden" name="plan_x"  value="" />
                    <input type="hidden" name="plan_y"  value="" />
                  </form>
                  <a href="#" onclick="PlanEtage.show('{{$_zone->chambre_id}}');">{{$_zone}}</a>
                  <div class="compact" style="display: block;white-space: pre-wrap;color:black;" >{{$_zone->caracteristiques|truncate:60}}</div>
                </div>
              </td>
            {{else}}
              <td data-x="{{$x}}" data-y="{{$y}}" class="conteneur-chambre" ></td>
            {{/if}}
         {{/foreach}}
        {{/foreach}}
        </tr>
      </div>
    </td>
  </tr>
</table>