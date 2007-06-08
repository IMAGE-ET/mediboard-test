{{assign var="operation" value=$object}}

<table class="tbl tooltip">
  <tr>
    <th>
      {{$object->_view}}
    </th>
  </tr>
  
  {{if $operation->annulee == 1}}
  <tr>
    <th class="category cancelled">
    {{tr}}COperation-annulee{{/tr}}
    </th>
  </tr>
  {{/if}}
  
  <tr>
    <td>
      <strong>{{tr}}COperation-date-court{{/tr}}:</strong>
      <i>le {{$object->_datetime|date_format:"%d %B %Y"}}</i>
      <br />
      <strong>{{tr}}COperation-chir_id-court{{/tr}}:</strong>
      <i>{{$object->_ref_chir->_view}}</i>
      <br />
      <strong>{{tr}}COperation-anesth_id-court{{/tr}}:</strong>
      <i>{{$object->_ref_anesth->_view}}</i>
      {{if $object->libelle}}
      <br />
      <strong>{{tr}}COperation-libelle{{/tr}}:</strong>
      <i>{{$object->libelle}}</i>
      {{/if}}
      <br />
      <strong>{{tr}}COperation-cote{{/tr}}:</strong>
      <i>{{tr}}{{$object->cote}}{{/tr}}</i>
      <br />
      <strong>{{tr}}COperation-_lu_type_anesth{{/tr}}:</strong>
      {{$object->_lu_type_anesth}}
      {{if $object->materiel}}
        <br />
        <strong>{{tr}}COperation-materiel-court{{/tr}}:</strong>
        <i>{{$object->materiel|nl2br|truncate:50}}</i>
      {{/if}}
      {{if $object->rques}}
        <br />
        <strong>{{tr}}COperation-rques-court{{/tr}}:</strong>
        <i>{{$object->rques|nl2br|truncate:50}}</i>
      {{/if}}
      <table width="100%" style="border-spacing: 0px;font-size: 100%;">
        {{assign var="vue" value="view"}}
        {{assign var="subject" value=$object}}
        {{include file="../../dPcabinet/templates/inc_list_actes.tpl"}}
      </table>
    </td>
  </tr>
</table>