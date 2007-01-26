<table class="tbl tooltip">
  <tr>
    <th>
      {{$object->_view}}
    </th>
  </tr>
  <tr>
    <td>
      <strong>Date:</strong>
      <i>le {{$object->_datetime|date_format:"%d %B %Y"}}</i>
      <br />
      <strong>Praticien:</strong>
      <i>{{$object->_ref_chir->_view}}</i>
      <br />
      <strong>Anesthésiste:</strong>
      <i>{{$object->_ref_anesth->_view}}</i>
      <br />
      <strong>Libellé:</strong>
      <i>{{$object->libelle}}</i>
      <br />
      <strong>Coté:</strong>
      <i>{{tr}}{{$object->cote}}{{/tr}}</i>
      <br />
      <strong>Type d'anesthésie:</strong>
      {{$object->_lu_type_anesth}}
      {{if $object->materiel}}
        <br />
        <strong>Materiel:</strong>
        <i>{{$object->materiel|nl2br}}</i>
      {{/if}}
      {{if $object->rques}}
        <br />
        <strong>Remarques:</strong>
        <i>{{$object->rques|nl2br}}</i>
      {{/if}}
      <table width="100%" style="border-spacing: 0px;font-size: 100%;">
        <tr>
          <th class="category">Actes CCAM</th>
        </tr>
        {{assign var="styleBorder" value="border: solid #aaa 1px;"}}
        {{foreach from=$object->_ext_codes_ccam item=currCode}}
        <tr>
          <td class="text" style="{{$styleBorder}}">
            <strong>{{$currCode->code}}</strong> :
            {{$currCode->libelleLong}}
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>