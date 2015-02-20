{{*
 * $Id$
 *  
 * @category Modèles
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<!-- Formulaire d'ajout de correspondant courrier par autocomplete -->
<form name="addCorrespondant" method="post">
  {{mb_class class="CCorrespondantCourrier"}}
  <input type="hidden" name="correspondant_courrier_id" />
  <input type="hidden" name="compte_rendu_id" value="" />
  <input type="hidden" name="object_class" value="CMedecin" />
  <input type="hidden" name="tag" value="correspondant" />
  <input type="hidden" name="object_id" />
</form>

<form name="addCorrespondantToDossier" method="post">
  <input type="hidden" name="m" value="patients"/>
  <input type="hidden" name="dosql" value="do_correspondant_aed" />
  <input type="hidden" name="patient_id" value="" />
  <input type="hidden" name="medecin_id" value="" />
</form>

<!-- Formulaire pour l'impression server side -->
<form name="print-server" method="post" action="?m=compteRendu&ajax_print_server">
  <input type="hidden" name="content" value=""/>
  <input type="hidden" name=""/>
</form>

<!-- Formulaire pour streamer le pdf -->
<form style="display: none;" name="download-pdf-form" method="post"
      target="{{if in_array($compte_rendu->factory, array("CDomPDFConverter", "CPrinceXMLConverter"))}}download_pdf{{else}}_blank{{/if}}"
      action="?m=compteRendu&a=ajax_pdf" onsubmit="{{if $pdf_thumbnails && $pdf_and_thumbs}}completeLayout();{{/if}} this.submit();">
  <input type="hidden" name="content" value=""/>
  <input type="hidden" name="compte_rendu_id" value='{{if $compte_rendu->_id != ''}}{{$compte_rendu->_id}}{{else}}{{$modele_id}}{{/if}}' />
  <input type="hidden" name="object_id" value="{{$compte_rendu->object_id}}"/>
  <input type="hidden" name="suppressHeaders" value="1"/>
  <input type="hidden" name="stream" value="1"/>
  <input type="hidden" name="page_format" value=""/>
  <input type="hidden" name="orientation" value=""/>
  <input type="hidden" name="_ids_corres" value="" />
</form>