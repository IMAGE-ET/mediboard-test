{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage sherpa
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<div class="big-info">
  L'import d'actes Sherpa est pour le moment <strong>silencieux</strong>.
  <br/>
  On ne fait qu'analyser le contenu de la requête sans effectuer l'ajout proprement dit.
  <br />Le format exact du token est : 
  <pre>CODPRA|CODACT|ACTIV|PHASE|MOD1|MOD2|MOD3|MOD4|ASSOC|DEPHON|DATEACT|EXTDOC|REMBEX|CODSIG</pre>
</div>

<div class="big-info">
  Exemple complet de passage de paramètres à la requête HTTP GET :
  <pre>
m=sherpa
a=import_actes
numdos=800008
actes[60268][]=214|NFKA007|1|0|K|J|7||0|56.6|2008-02-14T10:10:10||1|1
actes[60268][]=301|NFKA007|4|0|K|J|||1|10.0|2008-02-14T11:11:11||0|0
actes[60269][]=214|BFGA004|1|0||||0|0.0|1|2008-02-14T12:12:12||1|1</pre>
</div>

{{if $sejour->_id}}
<table class="tbl">
  <tr>
    <th class="category" colspan="10">{{$sejour->_view}}</th>
  </tr>
  
  <!-- Operations -->
	{{foreach from=$sejour->_ref_operations item=_operation}}
  <tr>
    <th class="category" colspan="10">
      Intervention du {{mb_value object=$_operation field=_datetime}}
    </th>
  </tr>

  <!-- Actes -->
	{{foreach from=$_operation->_ref_actes_ccam item=_acte}}
  <tr>
    <td>{{$_acte->code_acte}}</td>
    <td>{{$_acte->code_activite}}</td>
    <td>{{$_acte->code_phase}}</td>
  </tr>
	{{foreachelse}}
  <tr>
    <td class="text" colspan="10">
      <em>Aucun acte importé pour cette intervention</em>
    </td>
  </tr>
  {{/foreach}}

	{{foreachelse}}
  <tr>
    <td class="text" colspan="10">
      <em>Aucune intervention dans ce séjour</em>
    </td>
  </tr>
  {{/foreach}}
</table>

{{else}}
<div class="big-warning">
  Aucun séjour sélectionné
</div>
{{/if}}