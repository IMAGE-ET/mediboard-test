{{foreach from=$grille item=ligne}}
  <tr>
    {{foreach from=$ligne item=_zone }}
      {{if $_zone!="0" && $_zone->service_id == $key}}
       <td data-service-id="{{$_zone->service_id}}" data-chambre-id="{{$_zone->chambre_id}}" data-lit-id="{{foreach from=$_zone->_ref_lits item=i name=foo}}{{if $smarty.foreach.foo.first}}{{$i->_id}} {{/if}}{{/foreach}}" data-nb-lits="{{$_zone->_ref_lits|@count}}" 
         class="chambre" colspan="{{$_zone->_ref_emplacement->largeur}}" rowspan="{{$_zone->_ref_emplacement->hauteur}}" id="chambre-{{$_zone->chambre_id}}"
         style="background-color:#{{$_zone->_ref_emplacement->color}};width:{{$_zone->_ref_emplacement->largeur*120}}px;height:{{$_zone->_ref_emplacement->hauteur*80}}px;">
        <script>
          Main.add(function(){
            var container=$('chambre-{{$_zone->chambre_id}}');
            Droppables.add(container,{onDrop: function(element, zonedrop){ChoiceLit.savePlan(element, zonedrop);}});
          });
        </script>
        <small style="background-color:#{{$_zone->_ref_emplacement->color}};">{{$_zone}}</small>
        {{foreach from=$chambres_affectees item=_affectation}}
          {{if $_affectation->_ref_lit->_ref_chambre->nom==$_zone && $_affectation->_ref_lit->_ref_chambre->service_id == $key}}
            {{mb_include module=hospi template=inc_vw_patient_affectation}}
          {{/if}}
        {{/foreach}}
      </td> 
      {{else}}
      <td class="chambre"></td>
      {{/if}}
    {{/foreach}}
  </tr>
{{foreachelse}}
  Pas de plan existant pour ce service
{{/foreach}}