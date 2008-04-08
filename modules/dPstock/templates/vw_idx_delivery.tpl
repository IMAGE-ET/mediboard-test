{{mb_include_script module=dPstock script=product_selector}}
{{mb_include_script module=system script=object_selector}}
{{mb_include_script module=dPstock script=filter}}

<script type="text/javascript">
function pageMain() {
  regFieldCalendar("filter-deliveries", "_date_min");
  regFieldCalendar("filter-deliveries", "_date_max");
  
  filterFields = ["product_id", "_date_min", "_date_max", "target_class", "target_id", "keywords"];
  deliveriesFilter = new Filter("filter-deliveries", "{{$m}}", "httpreq_vw_deliveries_list", "list-deliveries", filterFields);
  deliveriesFilter.submit();
}
</script>

<table class="main">
  <tr>
    <td class="halfPane" rowspan="5">
      <form name="filter-deliveries" action="?" method="post" onsubmit="return deliveriesFilter.submit();">
      <input type="hidden" class="m" name="{{$m}}" />
        <table class="form">
          <tr>
            <th class="title" colspan="4">Recherche d'administrations de produits</th>
          </tr>
          <tr>
            <th>{{mb_title object=$delivery field=target_class}}</th>
            <td>
              <select class="notNull str" name="target_class">
                <option value="">&mdash; Choisissez un type</option>
                {{foreach from=$classes_list|smarty:nodefaults item=curr_class}}
                <option value="{{$curr_class}}">{{tr}}{{$curr_class}}{{/tr}}</option>
                {{/foreach}}
              </select>
            </td>
            <th>{{mb_title object=$delivery field=target_id}}</th>
            <td class="readonly">
              <input type="hidden" name="target_id" value="" class="{{$delivery->_props.target_id}}" />
              <input type="text" size="20" name="_view" readonly="readonly" value="" ondblclick="ObjectSelector.initFilter()" />
              <button type="button" onclick="ObjectSelector.initFilter()" class="search notext">Rechercher</button>
              <script type="text/javascript">
                ObjectSelector.initFilter = function() {
                  this.sForm     = "filter-deliveries";
                  this.sView     = "_view";
                  this.sId       = "target_id";
                  this.sClass    = "target_class";
                  this.pop();
                }
              </script>
            </td>
          </tr>
          <tr>
            <th>{{mb_title object=$delivery field=product_id}}</th>
            <td class="readonly">
              <input type="hidden" name="product_id" value="" />
              <input type="text" name="product_name" value="" size="20" readonly="readonly" ondblclick="ProductSelector.initFilter()" />
              <button class="search notext" type="button" onclick="ProductSelector.initFilter()">Rechercher</button>
              <script type="text/javascript">
              ProductSelector.initFilter = function(){
                this.sForm = "filter-deliveries";
                this.sId   = "product_id";
                this.sView = "product_name";
                this.pop();
              }
              </script>
            </td>
            <th>Mots clés</th>
            <td colspan="3"><input type="text" class="search" name="keywords" /></td>
          </tr>
          <tr>
            <th>{{mb_title object=$delivery field=_date_min}}</th>
            <td class="date">{{mb_field object=$delivery field=_date_min form=filter-deliveries}}</td>
            <th>{{mb_title object=$delivery field=_date_max}}</th>
            <td class="date">{{mb_field object=$delivery field=_date_max form=filter-deliveries}}</td>
          </tr>
          <tr>
            <td colspan="4" class="button">
              <button type="submit" class="search">Filtrer</button>
              <button type="button" class="cancel notext" onclick="deliveriesFilter.empty();">Remettre à zéro</button>
            </td>
          </tr>
        </table>
      </form>
    
      <div id="list-deliveries"></div>
    </td>
    
    <td class="halfPane">
    <a class="buttonnew" href="?m={{$m}}&amp;tab=vw_idx_delivery&amp;delivery_id=0">
      Nouvelle administration
    </a>
    <form name="edit-delivery" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="tab" value="vw_idx_delivery" />
      <input type="hidden" name="dosql" value="do_delivery_aed" />
      <input type="hidden" name="delivery_id" value="{{$delivery->_id}}" />
      {{if !$delivery->_id}}
      <input type="hidden" name="date" value="now" />
      {{/if}}
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $delivery->_id}}
          <th class="title modify" colspan="2">Modification de l'administration {{$delivery->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Nouvelle administration</th>
          {{/if}}
        </tr>   
        <tr>
          <th>{{mb_label object=$delivery field="product_id"}}</th>
          <td class="readonly">
            <input type="hidden" name="product_id" value="{{$delivery->_ref_product}}" class="{{$delivery->_props.product_id}}" />
            <input type="text" name="product_name" value="{{if $delivery->_ref_product}}{{$delivery->_ref_product->_view}}{{/if}}" size="40" readonly="readonly" ondblclick="ProductSelector.initEdit()" />
            <button class="search notext" type="button" onclick="ProductSelector.initEdit()">Rechercher</button>
            <script type="text/javascript">
            ProductSelector.initEdit = function(){
              this.sForm = "edit-delivery";
              this.sId   = "product_id";
              this.sView = "product_name";
              this.pop({{$delivery->product_id}});
            }
            </script>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$delivery field=target_class}}</th>
          <td class="readonly">{{mb_field object=$delivery field=target_class readonly="readonly" size="30"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$delivery field="target_id"}}</th>
          <td class="readonly">
            {{mb_field object=$delivery field=target_id hidden="1"}}
            <input type="text" size="40" name="_view" readonly="readonly" value="{{if $delivery->_ref_target}}{{$delivery->_ref_target->_view}}{{/if}}" ondblclick="ObjectSelector.initEdit()" />
            <button type="button" onclick="ObjectSelector.initEdit()" class="search notext">Rechercher</button>
            <script type="text/javascript">
              ObjectSelector.initEdit = function() {
                this.sForm     = "edit-delivery";
                this.sView     = "_view";
                this.sId       = "target_id";
                this.sClass    = "target_class";
                this.pop();
              }
            </script>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$delivery field="code"}}</th>
          <td>{{mb_field object=$delivery field="code"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$delivery field="description"}}</th>
          <td>{{mb_field object=$delivery field="description"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {{if $delivery->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la \'administration',objName:'{{$delivery->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr>  
      </table>
    </form>
    </td>
  </tr>
</table>
