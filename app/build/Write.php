<?php

class Write
{

  // Write `s` into bytes (ULEB128 & string)
  public function write_string(string $str)
  {

    if ($str) {
      $encoded = utf8_encode($str);
      $return = "\x0b" . $this->write_uleb128(strlen($encoded)) . $encoded;
    } else {
      $return = "\x00";
    }

    return $return;
  }

  // Write `num` into an unsigned LEB128.
  public function write_uleb128(int $num)
  {

    // return something I don't understand when number of param is 0
    if ($num == 0) {

      // use unpack() to create a new byte arra
      return unpack('C*', "\x00");
    }

    // init new array for bytes
    $return = [];

    // iterate through the number? idk
    while ($num != 0) {

      // push the number and something I don't understand to the byte array
      array_push($return, [$num, 0x7F]);

      // shift the bits of num by 7 places to the right. (Bitwise operator (>>) used)
      $num >>= 7;

      // change the byte at index -1 to the given
      if ($num != 0) {
        $return[-1] |= 0x80;
      }
    }

    return $return;
  }
}