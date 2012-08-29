<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage dicom
 *  @version $Revision$
 *  @author SARL OpenXtrem
 */

/**
 * A stream reader who can read several types from a binary stream, in Big Endian or Little Endian syntax
 */
class CDicomStreamReader {
  
  /**
   * The stream (usually a stream to the socket connexion)
   * 
   * @var resource
   */
  var $stream = null;
  
  /**
   * The content of the stream, used to keep a trace of the DICOM exchanges
   * 
   * @var binary data
   */
  var $buf = null;
  
  /**
   * The constructor of CDicomStreamReader
   * 
   * @param resource $stream The stream
   */
  function __construct($stream) {
    $this->stream = $stream;
    $this->buf = "";
  }
  
  /**
   * Move forward the stream pointer
   * 
   * @param int $bytes The number of bytes you want to skip. This number can't be negative
   */
  function skip($bytes) {
    if ($bytes > 0) {
      $this->buf .= fread($this->stream, $bytes);
    }
  }
  
  /**
   * Return the current position of the stream pointer.
   * 
   * @return int The current position of the stream pointer
   */
  function getPos() {
    return ftell($this->stream);
  }
  
  
  /**
   * Read hexadecimal numbers from the stream
   * 
   * @param int $length The length of the number, equal to 1 if not given
   * 
   * @param string $endianness Equal to BE if you need Big Endian, LE if Little Endian. Equal to BE if not given
   * 
   * @return hexadecimal number
   */
  function readHexByte($length = 1, $endianness = "BE") {
    if ($endianness == "BE") {
      return $this->readHexByteBE($length);
    } elseif ($endianness == "LE") {
      return $this->readHexByteLE($length);
    }
  }
  
  /**
   * Read hexadecimal numbers from the stream. Use Big Endian syntax
   * 
   * @param int $length The length of the number, equal to 1 if not given
   * 
   * @return hexadecimal number
   */
  function readHexByteBE($length = 1) {
    $tmp = fread($this->stream, $length);
    $this->buf .= $tmp;
    $tmp = unpack("H*", $tmp);
    return $tmp[1];
  }
  
  /**
   * Read hexadecimal numbers from the stream. Use Little Endian syntax
   * 
   * @param int $length The length of the number, equal to 1 if not given
   * 
   * @return hexadecimal number
   */
  function readHexByteLE($length = 1) {
    $tmp = fread($this->stream, $length);
    $this->buf .= $tmp;
    $tmp = unpack("h*", $tmp);
    return $tmp[1];
  }
  
  /**
   * Read unsigned 32 bits numbers.
   * 
   * @param string $endianness Equal to BE if you need Big Endian, LE if Little Endian. Equal to BE if not given
   * 
   * @return integer
   */
  function readUnsignedInt32($endianness = "BE") {
    if ($endianness == "BE") {
      return $this->readUnsignedInt32BE();
    } elseif ($endianness == "LE") {
      return $this->readUnsignedInt32LE();
    }
  }
  
  /**
   * Read unsigned 32 bits numbers, in Big Endian syntax.
   * 
   * @return integer
   */
  function readUnsignedInt32BE() {
    $tmp = fread($this->stream, 4);
    $this->buf .= $tmp;
    $tmp = unpack("N", $tmp);
    return $tmp[1];
  }
  
  /**
   * Read unsigned 32 bits numbers, in Little Endian syntax.
   * 
   * @return integer
   */
  function readUnsignedInt32LE() {
    $tmp = fread($this->stream, 4);
    $this->buf .= $tmp;
    $tmp = unpack("V", $tmp);
    return $tmp[1];
  }
  
  /**
   * Read unsigned 16 bits numbers.
   * 
   * @param string $endianness Equal to BE if you need Big Endian, LE if Little Endian. Equal to BE if not given
   * 
   * @return integer
   */
  function readUnsignedInt16($endianness = "BE") {
    if ($endianness == "BE") {
      return $this->readUnsignedInt16BE();
    } elseif ($endianness == "LE") {
      return $this->readUnsignedInt16LE();
    }
  }
  
  /**
   * Read unsigned 16 bits numbers, in Big Endian syntax.
   * 
   * @return integer
   */
  function readUnsignedInt16BE() {
    $tmp = fread($this->stream, 2);
    $this->buf .= $tmp;
    $tmp = unpack("n", $tmp);
    return $tmp[1];
  }
  
  /**
   * Read unsigned 16 bits numbers, in Big Endian syntax.
   * 
   * @return integer
   */
  function readUnsignedInt16LE() {
    $tmp = fread($this->stream, 2);
    $this->buf .= $tmp;
    $tmp = unpack("v", $tmp);
    return $tmp[1];
  }
  
  /**
   * Read unsigned 8 bits numbers.
   * 
   * @return integer
   */
  function readUnsignedInt8() {
    $tmp = fread($this->stream, 1);
    $this->buf .= $tmp;
    $tmp = unpack("C", $tmp);
    return $tmp[1];
  }
  
  /**
   * Read a string
   * 
   * @param int $length The length of the string
   * 
   * @return string
   */
  function readString($length) {
    $tmp = fread($this->stream, $length);
    $this->buf .= $tmp;
    $tmp = unpack("A*", $tmp);
    return $tmp[1];
  }
  
  /**
   * Read a Dicom UID (series of integer, separated by ".")
   * 
   * @param int $length The length of the UID, equal to 64 if not given
   * 
   * @return string
   */
  function readUID($length = 64) {
    $tmp = fread($this->stream, $length);
    $this->buf .= $tmp;
    $tmp = unpack("A*", $tmp);
    return $tmp[1];
  }
} 
?>