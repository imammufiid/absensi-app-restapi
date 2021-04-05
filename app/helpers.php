<?php

use App\Config;
use Illuminate\Support\Facades\File;

if (!function_exists('object_meta')) {
   function object_meta($code = 200, $status = "success", $message = "")
   {
      $meta = [
         'code' => $code,
         'status' => $status,
         'message' => $message
      ];
      return $meta;
   }
}

if (!function_exists('data_app_configuration')) {
   function data_app_configuration($nameConfiguration = "") {
      $data = Config::where('name', $nameConfiguration)->first();
      return $data;
   }
}

if (!function_exists('list_of_name_configuration')) {
   function list_of_name_configuration() {
      $data = Config::select('name')->get();
      return $data;
   }
}

if (!function_exists('create_folder_public')) {
   function create_folder_public($nameOfFolder = "") {
      $folderPath = public_path($nameOfFolder);
      if (!File::isDirectory($folderPath)) {
         File::makeDirectory($folderPath, 0777, true, true);
         return $folderPath;
      }
      return $folderPath;
   }
}

if (!function_exists('check_folder_public_if_exist')) {
   function check_folder_public_if_not_exist($nameOfFolder = "") {
      $folderPath = public_path($nameOfFolder);
      if (!File::isDirectory($folderPath)) {
         return true;
      }
      return false;
   }
}
