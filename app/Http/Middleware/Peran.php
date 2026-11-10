<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/** Pemakaian di route: Peran::class.':admin' atau Peran::class.':mahasiswa,dosen' */
class Peran
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $u = $request->user();
        if (!$u) {
            return redirect(in_array('admin', $roles) ? '/admin/login' : '/');
        }
        if (!in_array($u->role, $roles)) {
            return redirect($u->role === 'admin' ? '/admin' : '/dashboard');
        }
        return $next($request);
    }
}
