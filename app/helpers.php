<?php

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
