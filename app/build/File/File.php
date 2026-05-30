<?php

namespace Bruder\File;

use ZipArchive;

class File
{
  public function download(string $file, string $download_loaction)
  {
    $file_basename = basename($file);
    $get_file = file_get_contents($file);
    $file_data = file_put_contents($download_loaction . $file_basename, $get_file);

    if (!$file_data) return false;

    return true;
  }

  public function unzip(string $file, ?string $unzip_loaction = null, bool $delete_zip_file = true)
  {
    $zip = new ZipArchive;
    $zip_response = $zip->open($file);

    if (!$zip_response) return false;
    if (!$unzip_loaction) $unzip_loaction = (string) pathinfo($file, PATHINFO_DIRNAME);

    $extract = $zip->extractTo($unzip_loaction);

    if (!$extract) return false;

    $zip->close();

    if ($delete_zip_file) unlink($file);

    return true;
  }

  public function delete(string $file)
  {
    $delete = unlink($file);

    if (!$delete) return false;

    return true;
  }

  /**
   * Read and parse the JSON configuration file.
   *
   * @param string $filePath the path to the JSON configuration file.
   * @return object the configuration array.
   */
  public static function readJsonConfig($filePath)
  {
    $json = file_get_contents($filePath);
    return (object) json_decode($json);
  }
}
