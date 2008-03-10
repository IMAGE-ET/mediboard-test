<table class="main">
  <tr>
    <td class="halfPane">
      <a class="buttonnew" href="?m={{$m}}&amp;tab=vw_idx_order_manager&amp;order_id=0">
        Nouvelle commande
      </a>
      <h3>Commandes en attente</h3>
      <table class="tbl" id="waiting_orders">
        <tr>
          <th>Intitulé</th>
          <th>Fournisseur</th>
          <th>Pièces</th>
          <th>Total</th>
          <th>Bloquée</th>
          <th>Actions</th>
        </tr>
        <tbody>
        {{foreach from=$waiting_orders item=curr_order}}
          <tr id="order-{{$curr_order->_id}}">
            <td><a href="?m={{$m}}&amp;tab=vw_idx_order_manager&amp;order_id={{$curr_order->_id}}">{{if $curr_order->name}}{{$curr_order->name}}{{else}}Sans nom{{/if}}</a></td>
            <td>{{$curr_order->_ref_societe->_view}}</td>
            <td>{{$curr_order->_ref_order_items|@count}}</td>
            <td>{{$curr_order->_total|string_format:"%.2f"}}</td>
            <td>{{$curr_order->locked}}</td>
            <td>mod lock send</td>
          </tr>
        {{foreachelse}}
          <tr>
            <td colspan="8">Aucune commande</td>
          </tr>
        {{/foreach}}
        </tbody>
      </table>
      
      
      <h3>Commandes en attente de réception</h3>
      <table class="tbl" id="pending_orders">
        <tr>
          <th>Intitulé</th>
          <th>Fournisseur</th>
          <th>Pièces</th>
          <th>Passée le</th>
          <th>Partielle</th>
          <th>Total</th>
          <th>Actions</th>
        </tr>
        <tbody>
        {{foreach from=$pending_orders item=curr_order}}
          <tr id="order-{{$curr_order->_id}}">
            <td><a href="?m={{$m}}&amp;tab=vw_idx_order_manager&amp;order_id={{$curr_order->_id}}">{{if $curr_order->name}}{{$curr_order->name}}{{else}}Sans nom{{/if}}</a></td>
            <td>{{$curr_order->_ref_societe->_view}}</td>
            <td>{{$curr_order->_ref_order_items|@count}}</td>
            <td>{{$curr_order->date_ordered|date_format:"%d/%m/%Y"}}</td>
            <td>O/N</td>
            <td>{{$curr_order->_total|string_format:"%.2f"}}</td>
            <td>reçue</td>
          </tr>
        {{foreachelse}}
          <tr>
            <td colspan="8">Aucune commande</td>
          </tr>
        {{/foreach}}
        </tbody>
      </table>
      
      
      <h3>Anciennes commandes</h3>
      <table class="tbl" id="old_orders">
        <tr>
          <th>Intitulé</th>
          <th>Fournisseur</th>
          <th>Pièces</th>
          <th>Passée le</th>
          <th>Reçue le</th>
          <th>Total</th>
          <th>Actions</th>
        </tr>
        <tbody>
        {{foreach from=$old_orders item=curr_order}}
          <tr id="order-{{$curr_order->_id}}">
            <td><a href="?m={{$m}}&amp;tab=vw_idx_order_manager&amp;order_id={{$curr_order->_id}}">{{if $curr_order->name}}{{$curr_order->name}}{{else}}Sans nom{{/if}}</a></td>
            <td>{{$curr_order->_ref_societe->_view}}</td>
            <td>{{$curr_order->_ref_order_items|@count}}</td>
            <td>{{$curr_order->date_ordered|date_format:"%d/%m/%Y"}}</td>
            <td>{{$curr_order->_date_received|date_format:"%d/%m/%Y"}}</td>
            <td>{{$curr_order->_total|string_format:"%.2f"}}</td>
            <td>redo del</td>
          </tr>
        {{foreachelse}}
          <tr>
            <td colspan="8">Aucune commande</td>
          </tr>
        {{/foreach}}
        </tbody>
      </table>
    </td>
    
    <td class="halfPane">
    <form name="edit_order" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_order_aed" />
	  <input type="hidden" name="order_id" value="{{$order->_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $order->_id}}
          <th class="title modify" colspan="2">Modification de la commande {{$order->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Nouvelle commande</th>
          {{/if}}
        </tr>   
        <tr>
          <th>{{mb_label object=$order field="name"}}</th>
          <td>{{mb_field object=$order field="name"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$order field="societe_id"}}</th>
          <td><select name="societe_id" class="{{$order->_props.societe_id}}">
            <option value="">&mdash; Choisir une société</option>
            {{foreach from=$list_societes item=curr_societe}}
              <option value="{{$curr_societe->_id}}" {{if $order->societe_id == $curr_societe->_id}} selected="selected" {{/if}} >
              {{$curr_societe->_view}}
              </option>
            {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$order field="received"}}</th>
          <td>{{mb_field object=$order field="received"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$order field="locked"}}</th>
          <td>{{mb_field object=$order field="locked"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {{if $order->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la commande',objName:'{{$order->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{if !$order->locked}}
              <a class="buttonedit" href="?m={{$m}}&amp;tab=vw_idx_order&amp;order_id={{$order->_id}}">Peupler</a>
            {{/if}}
            {{/if}}
          </td>
        </tr>        
      </table>
    </form>
    </td>
  </tr>
</table>


