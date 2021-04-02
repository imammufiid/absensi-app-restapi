<?php

use App\Config;

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
