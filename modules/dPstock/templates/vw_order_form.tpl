{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage Stock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}
	
<style type="text/css">
{{include file=../../dPcompteRendu/css/print.css header=4 footer=3 nodebug=true}}

html {
  font-family: Arial, Helvetica, sans-serif;
}

.print td {
  font-size: 11px;
  font-family: Arial, Verdana, Geneva, Helvetica, sans-serif;
}

.grid {
  border-collapse: collapse;
  border-spacing: 0;
  width: 99%;
  margin: auto;
}

.grid td,
.grid th {
  border: 1px solid #ccc !important;
  padding: 2px;
  color: #000;
}

.grid th {
  width: 0.1%;
  font-size: 1.0em !important;
}
</style>

{{assign var=label value=$order->getLabel()}}

<table class="main">
  <tr>
    <td>
<hr />
<table class="form">
  <col style="width: 10%" />
  <col style="width: 40%" />
  <col style="width: 10%" />
  <col style="width: 40%" />
  
  <tr>
    <th>Date</th>
    <td>{{$smarty.now|date_format:$conf.datetime}}</td>
    <th>Numéro</th>
    <td>{{$order->order_number}}</td>
  </tr>
  
  <tr>
    <th rowspan="2">{{mb_label object=$order field=comments}}</th>
    <td rowspan="2">{{mb_value object=$order field=comments}}</td>
    <th>{{mb_label object=$order field=_customer_code}}</th>
    <td>{{mb_value object=$order field=_customer_code}}</td>
  </tr>
  
  <tr>
    <th>{{if $order->object_id}}{{mb_label object=$order field=object_id}}{{/if}}</th>
    <td>
      {{if !$order->_septic}}
        {{$order->_ref_object}}
      {{/if}}
    </td>
  </tr>
  
  <tr>
    <td colspan="4">
      <hr />
    </td>
  </tr>
  
  <tr>
    <th>Expéditeur</th>
    <td>
      {{assign var=address value=$order->_ref_address}}

      {{* Pharmacie *}}
      {{if $address instanceof CFunctions}}

        <strong>
          {{$address->_ref_group}}<br />
          {{$address}}
        </strong>

        <br />
        {{$address->adresse|nl2br}}<br />
        {{mb_value object=$address field=cp}} {{mb_value object=$address field=ville}}

      {{* Bloc *}}
      {{elseif $address instanceof CBlocOperatoire}}
        <strong>
          {{$address}}
        </strong>

        <br />
        {{$order->_ref_group->adresse|nl2br}}<br />
        {{mb_value object=$order->_ref_group field=cp}} {{mb_value object=$order->_ref_group field=ville}}

      {{* Etablissement *}}
      {{elseif $address instanceof CGroups}}
        <strong>
          {{$address}}
        </strong>

        <br />
        {{$address->adresse|nl2br}}<br />
        {{mb_value object=$address field=cp}} {{mb_value object=$address field=ville}}
      {{/if}}

      <br />
      {{if $address->tel}}
        <br />{{mb_title object=$address field=tel}}: {{mb_value object=$address field=tel}}
      {{/if}}

      {{if $address->fax}}
        <br />{{mb_title object=$address field=fax}}: {{mb_value object=$address field=fax}}
      {{/if}}
      
      {{if $address instanceof CFunctions && $address->soustitre}}
        <hr />{{$address->soustitre|nl2br}}
      {{/if}}
    </td>
    
    <th>Fournisseur</th>
    <td>
      {{assign var=societe value=$order->_ref_societe}}
      <strong>{{mb_value object=$societe field=name}}</strong><br />
      {{$societe->address|nl2br}}<br />
      {{mb_value object=$societe field=postal_code}} {{mb_value object=$societe field=city}}
      
      <br />
      {{if $societe->phone}}
        <br />{{mb_title object=$societe field=phone}}: {{mb_value object=$societe field=phone}}
      {{/if}}
      
      {{if $societe->fax}}
        <br />{{mb_title object=$societe field=fax}}: {{mb_value object=$societe field=fax}}
      {{/if}}
    </td>
  </tr>
</table>
    </td>
  </tr>
  <thead>
    <tr>
      <td>
        <h2>
          <a href="#" onclick="window.print();">
            {{$label}} - {{$order->_ref_group}}
          </a>
        </h2>
      </td>
    </tr>
  </thead>
  
  <tfoot>
    <tr>
      <td>
        <span style="float: right; text-align: right;">
          {{$smarty.now|date_format:$conf.datetime}}
          
          {{if $pharmacien->_id}}
            <br />
            Pharmacien : <strong>{{$pharmacien}}</strong>
            {{if $pharmacien->commentaires}}
              - {{$pharmacien->commentaires}}
            {{/if}}
          {{/if}}
        </span>
        
        {{$label}} n° <strong>{{$order->order_number}}</strong>
        <br />
        Responsable : <strong>{{$app->_ref_user}}</strong>
      </td>
    </tr>
  </tfoot>
  
  <tr>
    <td>
      {{mb_include module=stock template=inc_order_items_list}}
    </td>
  </tr>
</table>