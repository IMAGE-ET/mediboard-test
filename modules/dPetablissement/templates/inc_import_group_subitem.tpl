{{if $allow_create}}
  <button class="new" type="button" onclick="this.up('div').select('select').each(function(select){$V(select, '__create__');})">Tout créer</button>
{{/if}}

<button class="lookup" type="button" onclick="this.up('div').select('select').each(function(select){select.selectedIndex=0;})">Tout retrouver</button>
<button class="cancel" type="button" onclick="this.up('div').select('select').each(function(select){$V(select, '__ignore__');})">Tout ignorer</button>

<table class="main tbl">
  <tr>
    <th class="category" style="width: 50%;">Présent dans le fichier</th>
    <th class="category">Présent en base</th>
  </tr>
  {{foreach from=$objects item=_object key=_key}}
    <tr>
      <td>{{$_object.values.$field}}</td>
      <td style="padding: 1px;">
        <select name="fromdb[{{$_key}}]" style="width: 30em; margin: 0;">
          {{foreach from=$_object.similar item=_similar}}
            <option value="{{$_similar->_guid}}">{{$_similar}}</option>
          {{/foreach}}

          {{if $allow_create}}
            <option value="__create__"> &mdash; Créer (renommé si déjà présent) &mdash; </option>
          {{/if}}

          <option value="__ignore__" selected> &mdash; Ignorer &mdash; </option>

          {{if $all_objects|@count}}
            <optgroup label="Autres">
              {{foreach from=$all_objects item=_object_item}}
                <option value="{{$_object_item->_guid}}">{{$_object_item}} (#{{$_object_item->_id}})</option>
              {{/foreach}}
            </optgroup>
          {{/if}}
        </select>
      </td>
    </tr>
  {{/foreach}}
</table>