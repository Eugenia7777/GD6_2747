use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

RateLimiter::for('login', function (Request $request) {
    return Limit::perMinute(5)
        ->by($request->input('email').'|'.$request->ip());
});

