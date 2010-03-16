<?php

class CMb128BObject extends C128BObject {
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
