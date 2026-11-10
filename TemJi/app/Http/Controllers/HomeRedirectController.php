<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HomeRedirectController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        return $user
            ? redirect()->route($user->getDefaultRedirectRoute())
            : redirect()->route('login');
    }
}
