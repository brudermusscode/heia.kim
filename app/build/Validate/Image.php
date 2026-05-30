<?php

namespace Bruder\Validate;

class Image
{

  /**
   * @param string $file_path
   * @return bool
   */
  public static function validate($file_path)
  {
    // Get image information
    $imageInfo = @getimagesize($file_path);

    // Check if getimagesize was successful and the file is an image
    return $imageInfo !== false && in_array($imageInfo[2], [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true);
  }

  /**
   * @param string $file_path
   * @return ?array
   */
  public static function info($file_path)
  {
    return @getimagesize($file_path);
  }

  /**
   * @param string $file_path
   * @return ?array
   */
  public static function extension($file_path)
  {
    $imageInfo = @getimagesize($file_path);

    if ($imageInfo !== false) {
      $imageType = $imageInfo[2];
      $extension = image_type_to_extension($imageType, false);

      if ($extension === 'jpeg')
        $extension = 'jpg';

      return $extension;
    }

    return null;
  }
}
