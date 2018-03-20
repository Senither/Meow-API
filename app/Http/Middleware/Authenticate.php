<?php

namespace App\Http\Middleware;

use App\Log;
use Closure;
use RuntimeException;
use Illuminate\Support\Str;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\InteractsWithTime;
use Illuminate\Contracts\Auth\Factory as Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

class Authenticate
{
    use InteractsWithTime;

    /**
     * The rate limiter instance.
     *
     * @var \Illuminate\Cache\RateLimiter
     */
    protected $limiter;

    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * The amount of minutes the rate limit should be decaying for.
     *
     * @var integer
     */
    protected $decayMinutes = 1;

    /**
     * The default cost a user can consume within the decayed time before getting rate limited.
     *
     * @var integer
     */
    protected $maxAttempts = 60;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth, RateLimiter $limiter)
    {
        $this->auth = $auth;
        $this->limiter = $limiter;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $cost = 1)
    {
        $key = $this->resolveRequestSignature($request);

        if (! $this->auth->guard(null)->guest()) {
            $this->maxAttempts = $request->user()->maxAttempts;
        }

        if ($this->limiter->tooManyAttempts($key, $this->maxAttempts)) {
            $this->incrementTooManyAttemptsFor($key);

            return $this->buildTooManyAttempts($key, $this->maxAttempts);
        }

        for ($i = $cost - 1; $i >= 0; $i--) { 
            $this->limiter->hit($key, $this->decayMinutes);
        }
        
        $response = $next($request);
        
        return $this->addHeaders(
            $response, $this->maxAttempts,
            $this->calculateRemainingAttempts($key, $this->maxAttempts)
        );
    }

    /**
     * Resolve request signature.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     * @throws \RuntimeException
     */
    protected function resolveRequestSignature($request)
    {
        if ($user = $request->user()) {
            return 'token|' . $user->token;
        }

        if ($route = $request->route()) {
            return $request->method() .
                '|' . $request->server('SERVER_NAME') .
                '|' . $request->ip();
        }

        throw new RuntimeException('Unable to generate the request signature. Route unavailable.');
    }

    /**
     * Create a 'too many attempts' error message.
     *
     * @param  string  $key
     * @param  int  $maxAttempts
     * @return \Illuminate\Http\Exceptions\ThrottleRequestsException
     */
    protected function buildTooManyAttempts($key, $maxAttempts)
    {
        $retryAfter = $this->getTimeUntilNextRetry($key);

        $headers = $this->getHeaders(
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts, $retryAfter),
            $retryAfter
        );

        return response()->json([
            'status' => 429,
            'reason' => 'Too Many Attempts'
        ], 429, $headers);
    }

    /**
     * Get the number of seconds until the next retry.
     *
     * @param  string  $key
     * @return int
     */
    protected function getTimeUntilNextRetry($key)
    {
        return $this->limiter->availableIn($key);
    }

    /**
     * Add the limit header information to the given response.
     *
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @param  int  $maxAttempts
     * @param  int  $remainingAttempts
     * @param  int|null  $retryAfter
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function addHeaders(Response $response, $maxAttempts, $remainingAttempts, $retryAfter = null)
    {
        $response->headers->add(
            $this->getHeaders($maxAttempts, $remainingAttempts, $retryAfter)
        );

        return $response;
    }

    /**
     * Get the limit headers information.
     *
     * @param  int  $maxAttempts
     * @param  int  $remainingAttempts
     * @param  int|null  $retryAfter
     * @return array
     */
    protected function getHeaders($maxAttempts, $remainingAttempts, $retryAfter = null)
    {
        $headers = [
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
        ];

        if (! is_null($retryAfter)) {
            $headers['Retry-After'] = $retryAfter;
            $headers['X-RateLimit-Reset'] = $this->availableAt($retryAfter);
        }

        return $headers;
    }

    /**
     * Calculate the number of remaining attempts.
     *
     * @param  string  $key
     * @param  int  $maxAttempts
     * @param  int|null  $retryAfter
     * @return int
     */
    protected function calculateRemainingAttempts($key, $maxAttempts, $retryAfter = null)
    {
        if (is_null($retryAfter)) {
            return $this->limiter->retriesLeft($key, $maxAttempts);
        }

        return 0;
    }

    /**
     * Increments the too-many-attempts value for the given key if it
     * exists, or creates it in the database.
     * 
     * @param  string  $key
     */
    protected function incrementTooManyAttemptsFor($key)
    {
        $log = Log::firstOrNew([
            'agent' => $key
        ]);

        $log->attempts = $log->attempts == null ? 1 : $log->attempts + 1;
        $log->save();
    }
}
