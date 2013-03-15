<table class="main">
  <tr>
    <td style="width: 50%;">
      
<!-- Eléments de prescription -->      
<table class="tbl">
  <tr>
    <th class="title">Eléments de prescription paramétrés</th>
  </tr>
  {{foreach from=$activite->_ref_elements_by_cat item=_elements_by_cat}}
    {{foreach from=$_elements_by_cat item=_element name="foreach_elt"}}
      {{assign var=elt_prescription value=$_element->_ref_element_prescription}}
      {{if $smarty.foreach.foreach_elt.first}}
      <tr>
        <th class="text">{{$elt_prescription->_ref_category_prescription}}</th>
      </tr>
      {{/if}}
      <tr>
        <td class="text">
          <span onmouseover="ObjectTooltip.createEx(this, '{{$elt_prescription->_guid}}')">
            {{$elt_prescription}}
          </span>
        </td>
      </tr>
    {{/foreach}}
  {{foreachelse}}
  <tr>
    <td class="empty">Aucun acte paramétré</td>
  </tr>
  {{/foreach}}
</table>

    </td>

    <td style="width: 50%;">

<!-- Eléments de prescription -->      
<div style="max-height: 500px; overflow-y: auto;">
  
<table class="tbl">
  <tr>
    <th colspan="2" class="title">Nombre d'actes réalisés par exécutant</th>
  </tr>
  {{foreach from=$activite->_count_actes_by_executant key=_executant_id item=_count}}
  {{if isset($activite->_ref_all_executants.$_executant_id|smarty:nodefaults)}}
  <tr>
    <td>
       {{assign var=executant value=$activite->_ref_all_executants.$_executant_id}}
       {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$executant}} 
    </td>
    <td style="text-align: center;">{{$_count}}</td>
  </tr>   
  {{/if}}
  {{foreachelse}}
  <tr>
    <td class="empty">Aucun exécutant</td>
  </tr>
  {{/foreach}}
</table>
      
    </td>
  </tr>
  
</table>

</div>
