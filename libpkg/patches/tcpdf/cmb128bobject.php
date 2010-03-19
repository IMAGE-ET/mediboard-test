<?php

class CMb128BObject extends C128BObject {
  public function DrawObject($xres) {
    $oldValue = $this->mValue;
    
    // FIXME: doit etre supprimé lors de la mise à jour de TCPDF
    $chars = str_split($this->mValue);
    foreach($chars as $offset => $char) {
      $this->mValue[$offset] = chr(ord($char)-2);
    }
    
    $return = parent::DrawObject($xres);
    
    $this->mValue = $oldValue;
    
    return $return;
  }
  
  // FIXME: doit etre supprimé lors de la mise à jour de TCPDF
  protected function DrawText($Font, $xPos, $yPos, $Char) {
    $chars = str_split($Char);
    foreach($chars as $offset => $char) {
      $Char[$offset] = chr(ord($char)+2);
    }
    
    ImageString($this->mImg,$Font,$xPos,$yPos,$Char,$this->mBrush);
  }
  
  // FIXME: doit etre supprimé lors de la mise à jour de TCPDF
  protected function DrawChar($Font, $xPos, $yPos, $Char) {
    $chars = str_split($Char);
    foreach($chars as $offset => $char) {
      $Char[$offset] = chr(ord($char)+2);
    }
    
    ImageString($this->mImg,$Font,$xPos,$yPos,$Char,$this->mBrush);
  }
  
  public function FlushObject() {
    if (($this->mStyle & BCS_BORDER)) {
      $this->DrawBorder();
    }
    if ($this->mStyle & BCS_IMAGE_PNG) {
      ImagePng($this->mImg);
    } else if ($this->mStyle & BCS_IMAGE_JPEG) {
      ImageJpeg($this->mImg);
    }
  }
}
