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
          <th class="category" colspan="4">Actes CCAM</th>
        </tr>
        {{assign var="styleBorder" value="border: solid #aaa 1px;"}}
        <tr>
          <th style="{{$styleBorder}}text-align:left;">Code</th>
          <th style="{{$styleBorder}}text-align:left;">Exécutant</th>
          <th style="{{$styleBorder}}text-align:left;">Activité</th>
          <th style="{{$styleBorder}}text-align:left;">Phase &mdash; Modificateurs</th>
        </tr>
        {{foreach from=$object->_ref_actes_ccam item=currActe}}
        <tr>
          <td class="text" style="{{$styleBorder}}">
            <strong>{{$currActe->code_acte}}</strong><br />
            {{$currActe->_ref_code_ccam->libelleLong}}
          </td>
          <td class="text" style="{{$styleBorder}}">{{$currActe->_ref_executant->_view}}</td>
          <td style="{{$styleBorder}}">{{$currActe->code_activite}}</td>
          <td style="{{$styleBorder}}">
            {{$currActe->code_phase}}
            {{if $currActe->modificateurs}}
            &mdash; {{$currActe->modificateurs}}
            {{/if}}
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>