<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('route-url', function (Request $request) {
  try {
    return route($request->route, $request->segments);
  } catch (\Exception $e) {
  }
});