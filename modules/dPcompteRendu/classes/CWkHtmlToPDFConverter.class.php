<?php

/**
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author
 */ 

class CWkHtmlToPDFConverter extends CHtmlToPDFConverter {
  
  var $file           = null;
  var $width          = null;
  var $height         = null;
  var $format         = null;
  var $header         = null;
  var $header_height  = null;
  var $header_spacing = 0;
  var $footer         = null;
  var $footer_height  = null;
  var $footer_spacing = 0;
  var $body           = null;
  var $margins        = null;
  var $temp_name      = null;
  
  function prepare($format, $orientation) {
    global $rootName;
    
    // Changer les srs pour les images
    $this->html = preg_replace("/src=\"\/".$rootName."/","src=\"../", $this->html);
    
    if (is_array($format)) {
      $this->width  = $format[2];
      $this->height = $format[3];
    }
    else {
      $this->format = $format;
      $this->orientation = $orientation; 
    }
    
    // Racine du ou des fichiers html
    $this->temp_name = tempnam("./tmp", "wkhtmltopdf");
    
    // Extraire les marges
    preg_match("/@page\s*{\s*margin-top:\s*([0-9.]+)cm;\s*margin-right:\s*([0-9.]+)cm;\s*margin-bottom:\s*([0-9.]+)cm;\s*margin-left:\s*([0-9.]+)cm;/", $this->html,$matches);
    
    // Le facteur 10 est pour la conversion en mm
    $this->margins = array(
      "top"    => $matches[1] * 10,
      "right"  => $matches[2] * 10,
      "bottom" => $matches[3] * 10,
      "left"   => $matches[4] * 10
    );
    
    $pos_header = strpos($this->html, "<div id=\"header\"");
    $pos_footer = strpos($this->html, "<div id=\"footer\"");
    $pos_body   = strpos($this->html, "<div id=\"body\">");
    
    /* header / footer sans body */
    if (!$pos_body) {
      $pos_body = strlen($this->html) - 16;
    }
    
    $header     = null;
    $footer     = null;
    $header_footer_common = null;
    $page_number = "<script type='text/javascript'>
      function subst() {
        var vars={};
        var x=document.location.search.substring(1).split('&');
        for (var i in x) {var z=x[i].split('=',2);vars[z[0]] = unescape(z[1]);}
        var x=['page'];
        for (var i in x) {
          var y = document.getElementsByClassName(x[i]);
          for(var j=0; j<y.length; ++j) y[j].textContent = vars[x[i]];
        }
      }
    </script>";
    // Extraire l'entête
    if ($pos_header) {
      $header_footer_common = substr($this->html, 0, $pos_header);
      if ($pos_footer) {
        $header = substr($this->html, $pos_header, $pos_footer - $pos_header);
      }
      else {
        $header = substr($this->html, $pos_header, $pos_body - $pos_header);
      }
      
      // On trouve la taille du header dans le style
      preg_match("/#header\s*\{\s*height:\s*([0-9]+[\.0-9]*)px;/", $this->html, $matches);
      $this->header_height = $matches[1];
    }
    
    // Extraire le pied de page
    if ($pos_footer) {
      if (!$pos_header) {
        $header_footer_common = substr($this->html, 0, $pos_footer);
      }
      $footer = substr($this->html, $pos_footer, $pos_body - $pos_footer);
      
      $this->html = str_replace($footer, '', $this->html);
      
      preg_match("/#footer\s*{\s*height:\s*([0-9]+[\.0-9]*)px;/", $this->html, $matches);
      $this->footer_height = $matches[1];
    }
    
    // Supprimer le padding-top du body et du hr
    $this->html = preg_replace("/#body\s*\{\s*padding-top:\s*[0-9]*px;\s*\}/", "", $this->html);
    $this->html = preg_replace("/hr.pagebreak\s*{\s*padding-top:\s*[0-9]*px;\s*}/", "", $this->html);
    
    // Supprimer le padding-bottom du body
    $this->html = preg_replace("/#body\s*\{\s*padding-bottom:\s*[0-9]*px;\s*\}/", "", $this->html);
    
    // Suppression de la balise script pour l'impression
    $this->html = preg_replace("/(<script type=[\'\"]text\/javascript[\'\"]>.*<\/script>)/msU", "", $this->html);
    
    // Suppression du position fixed du header et du footer
    if ($header_footer_common) {
      $header_footer_common = preg_replace("/position:\s*fixed;/", "", $header_footer_common);
      $header_footer_common = preg_replace("/(<script type=[\'\"]text\/javascript[\'\"]>.*<\/script>)/msU", "", $header_footer_common);
    }
    
    // Store de l'entête / pied de page
    if ($header) {
      // On supprime l'entête que maintenant sinon les positions de chaînes seront erronées
      $this->html = str_replace($header, '', $this->html);
      $this->header = $this->temp_name . "-header.html";
      file_put_contents($this->header, $header_footer_common.$page_number.$header."</body></html>");
    }
    if ($footer) {
      $this->footer = $this->temp_name . "-footer.html";
      file_put_contents($this->footer, $header_footer_common.$page_number.$footer."</body></html>");
    }
    
    $this->file = $this->temp_name.".html";
    
    if (!$pos_body) {
      $this->html = $header_footer_common . "</body></html>";
    }
    
    file_put_contents($this->file, $this->html);
    
  }
  
  function render() {
    global $root_dir;
    
    $command = "$root_dir/lib/wkhtmltopdf/wkhtmltopdf-".CAppUI::conf("dPcompteRendu CCompteRendu arch_wkhtmltopdf")." ";
    $result = tempnam("./tmp", "result");
    $options = "--print-media-type ";

    // Entête
    if ($this->header) {
      $this->margins["top"] += (25.4*$this->header_height)/96;
      $options .= "--header-html $this->header --header-spacing $this->header_spacing ";
    }

    // Pied de page
    if ($this->footer) {
      $this->margins["bottom"] += ((25.4*$this->footer_height)/96 + $this->footer_spacing); 
      $options .= "--footer-html $this->footer --footer-spacing $this->footer_spacing ";
    }
    
    // Marges
    foreach ($this->margins as $key=>$_marge) {
      $options .= "--margin-$key $_marge ";
    }
    
    // Format de la page
    if ($this->format && $this->orientation) {
      $options .= "--page-size $this->format --orientation $this->orientation ";
    }
    
    if ($this->width && $this->height) {
      // Conversion en mm
      $width = (25.4*$this->width)/72;
      $height = (25.4*$this->height)/72;
      $options .= "--page-width $width --page-height $height ";
    }
    
    $options .= "$this->file $result";
    
    exec($command.$options);
    
    $this->result = file_get_contents($result);
    
    
    // Supression des fichiers temporaires
    @unlink($this->temp_name);
    @unlink($this->header);
    @unlink($this->footer);
    @unlink($this->file);
    @unlink($result);
  }

}
?>