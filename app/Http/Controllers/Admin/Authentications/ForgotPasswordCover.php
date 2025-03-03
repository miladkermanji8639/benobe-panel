<?php

namespace App\Http\Controllers\Admin\authentications;

use App\Http\Controllers\Admin\Controller;
use Illuminate\Http\Request;

class ForgotPasswordCover extends Controller
{
  public function index()
  {
    $pageConfigs = ['myLayout' => 'blank'];
    return view('admin.content.authentications.auth-forgot-password-cover', ['pageConfigs' => $pageConfigs]);
  }
}
