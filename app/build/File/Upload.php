<?php

namespace Heiakim\File;

class Upload
{

  /**
   * @param int $code
   * @return ?string
   */
  public static function error(int $code)
  {
    return match ($code) {
      2,
      1 => "<strong>Your image is too big!</strong> Scale it down or choose another one.",
      3 => "<strong>Your upload failed partially.</strong> Try again!",
      4 => "<strong>There was no file uploaded.</strong> What happened?",
      6 => "<strong>The dev has missed creating a temporary folder.</strong> You want to tell him?",
      7 => "<strong>Failed to write file to disk.</strong> What happened?",
      8 => "<strong>Something stopped the upload of your file.</strong> Try again!",
      default => null // Unknown error.
    };
  }
}
