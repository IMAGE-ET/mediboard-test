{{*
 * $Id$
 *  
 * @category Soins
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{assign var=patient value=$sejour->_ref_patient}}
{{assign var=prescription value=$sejour->_ref_prescription_sejour}}

{{math equation="4*x" x=$period assign=colspan}}
{{math equation="75/x" x=$colspan assign=width_th}}

<table class="main">
  {{* Entête *}}
  <thead>
    <tr>
      <td colspan="2">
        <h1 style="page-break-after: auto; text-align: center;">
          Impression du {{$now|date_format:$conf.longdate}} à {{$now|date_format:$conf.time}}
        </h1>
      </td>
    </tr>
    <tr>
      <td style="width: 50%">
        Civilité : {{mb_value object=$patient field=nom}} ({{mb_value object=$patient field=nom_jeune_fille}}) {{mb_value object=$patient field=prenom}}
        <br />
        Né(e) le : {{mb_value object=$patient field=naissance}}
      </td>
      <td>
        A : {{$sejour->_NDA}}
        <br />
        Date d'entrée : {{$sejour->entree|date_format:$conf.date}} à {{$sejour->entree|date_format:$conf.time}}
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <h2><strong>SERVICE :</strong> {{mb_value object=$service field=nom}}</h2>
      </td>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td colspan="2">
        <table class="tbl">
          <tr>
            <th class="title" style="width: 20%" colspan="3">
              PRESCRIPTION
            </th>
            {{*
            <th class="title" style="width: 10%" colspan="2">
              PRES. Post Op
            </th>
            *}}
            <th class="title" colspan="{{$colspan}}">
              ADMINISTRATION
            </th>
          </tr>
          <tr>
            <th class="text" style="width: 3%" rowspan="2">
              Presc.
            </th>
            <th class="text" rowspan="2" style="width: 12%">
              Libellé médicament <br />
              Posologie <br />
              Commentaires <br />
            </th>
            <th style="width: 10%" rowspan="2">
              Remarques
            </th>
            {{*<th class="text" rowspan="2">
              Poursuite du traitement personnel ou arrêt
            </th>
            <th class="text" rowspan="2">
              Date/heure <br />
              Nom <br />
              Signature médecin
            </th>*}}
            {{foreach from=$dates item=_date}}
              <th colspan="4">
                {{$_date|date_format:"%a"|substr:0:1|strtoupper}} <br />
                {{$_date|date_format:$conf.date}}
              </th>
            {{/foreach}}
          </tr>
          <tr>
            {{foreach from=$dates item=_date}}
              <th style="width: {{$width_th}}%">M</th>
              <th style="width: {{$width_th}}%">M</th>
              <th style="width: {{$width_th}}%">S</th>
              <th style="width: {{$width_th}}%">N</th>
            {{/foreach}}
          </tr>

          {{* Parcours des lignes *}}

          {{* Lignes de médicament *}}
          {{foreach from=$prescription->_ref_lines_med_for_plan item=_cat_ATC key=_key_cat_ATC}}
            {{foreach from=$_cat_ATC item=lines}}
              {{foreach from=$lines key=unite_prise item=line}}
                {{mb_include module=soins template=inc_offline_vw_line}}
              {{/foreach}}
            {{/foreach}}
          {{/foreach}}

          {{* Lignes de perfusion *}}
          {{foreach from=$prescription->_ref_prescription_line_mixes_for_plan item=line}}
            {{mb_include module=soins template=inc_offline_vw_line}}
          {{/foreach}}

          {{* Lignes d'éléments *}}
          {{foreach from=$prescription->_ref_lines_elt_for_plan item=elements_chap}}
            {{foreach from=$elements_chap item=elements_cat}}
              {{foreach from=$elements_cat item=_element}}
                {{foreach from=$_element key=unite_prise item=line}}
                  {{mb_include module=soins template=inc_offline_vw_line}}
                {{/foreach}}
              {{/foreach}}
            {{/foreach}}
          {{/foreach}}

          {{* Dernière ligne avec les initiales *}}
          <tr>
            <td colspan="3" style="text-align: right;">Initiales :</td>
            {{foreach from=$dates item=_date}}
              <td>&nbsp;</td>
              <td></td>
              <td></td>
              <td></td>
            {{/foreach}}
          </tr>
        </table>
      </td>
    </tr>
  </tbody>
</table>
