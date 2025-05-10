<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class PanelRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $currentPanel = Filament::getCurrentPanel()?->getId();

        if(!$user || !$currentPanel ){
            return redirect()->route('filament.auth.auth.login');
           // return redirect(Filament::getPanel('auth')->getLoginUrl());
        }
        $panelRoles = [
            'admin' => 'super_admin',
            'guest' => 'guest',
        ];

        $reqRole  = $panelRoles[$currentPanel] ?? null;

      

        if(!$reqRole || !$user->hasRole($reqRole) ){
            if($user->hasRole('super_admin')){
                return redirect()->route('filament.admin.pages.dashboard');
               
            }

            if($user->hasRole('guest')){
                return redirect()->route('filament.guest.pages.dashboard');
               
             }
             
              Session::flush(); // clear ang session
            //  Cookie::queue(Cookie::forget(config('session.cookie'))); // clear ang cookie
            return redirect()->route('filament.auth.auth.login');
            // abort(403, 'Unauthorized access to this panel.');
          
 
        }
        return $next($request);
    }
}
