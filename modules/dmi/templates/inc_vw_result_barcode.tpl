{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $isPhastInstalled}}
  {{if @$phast}}
  <script type="text/javascript">
    barcodes["{{$barcode}}"].phast = {{$object|@json}};
    
    var tab = [];
    var hashPhast = $H(barcodes["{{$barcode}}"].phast);
    hashPhast.each(function (p) { 
      if (p.value != barcodes["{{$barcode}}"].good[p.key])
        if (p.key != "type") 
          tab.push(p.key);
    });
    
    var className = (tab.length > 0) ? "error" : "ok";
    
    $('phast_barcode_{{$index}}').addClassName(className);
  </script>
  {{/if}}
{{/if}}

<table class="main layout">
  {{if isset($object.scc|smarty:nodefaults)}}
  <tr>
    <td style="width: 50%; text-align:right">GTIN</td>
    <td>{{$object.scc}}</td>
  </tr>
  {{/if}}
  {{if isset($object.ref|smarty:nodefaults)}}
  <tr>
    <td style="width: 50%; text-align:right">Référence / PCN</td>
    <td>{{$object.ref}}</td>
  </tr>
  {{/if}}
  {{if isset($object.per|smarty:nodefaults)}}
  <tr>
    <td style="width: 50%; text-align:right">Date péremption</td>
    <td>{{$object.per}}</td>
  </tr>
  {{/if}}
  {{if isset($object.lot|smarty:nodefaults)}}
  <tr>
    <td style="width: 50%; text-align:right">Numéro de lot</td>
    <td>{{$object.lot}}</td>
  </tr>
  {{/if}}
  {{if isset($object.type|smarty:nodefaults)}}
  <tr>
    <td style="width: 50%; text-align:right">Type</td>
    <td>{{$object.type}}</td>
  </tr>
  {{/if}}
</table>