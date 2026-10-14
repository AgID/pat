<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System\Http\Message {
    interface MessageInterface
    {
        public function getProtocolVersion();
        public function withProtocolVersion($version);
        public function getHeaders();
        public function hasHeader($name);
        public function getHeader($name);
        public function getHeaderLine($name);
        public function withHeader($name, $value);
        public function withAddedHeader($name, $value);
        public function withoutHeader($name);
        public function getBody();
        public function withBody(StreamInterface $body);
    }
    interface RequestInterface extends MessageInterface
    {
        public function getRequestTarget();
        public function withRequestTarget($requestTarget);
        public function getMethod();
        public function withMethod($method);
        public function getUri();
        public function withUri(UriInterface $uri, $preserveHost = false);
    }
    interface ResponseInterface extends MessageInterface
    {
        public function getStatusCode();
        public function withStatus($code, $reasonPhrase = '');
        public function getReasonPhrase();
    }
    interface StreamInterface
    {
        public function __toString();
        public function close();
        public function detach();
        public function getSize();
        public function tell();
        public function eof();
        public function isSeekable();
        public function seek($offset, $whence = SEEK_SET);
        public function rewind();
        public function isWritable();
        public function write($string);
        public function isReadable();
        public function read($length);
        public function getContents();
        public function getMetadata($key = null);
    }
    interface UriInterface
    {
        public function getScheme();
        public function getAuthority();
        public function getUserInfo();
        public function getHost();
        public function getPort();
        public function getPath();
        public function getQuery();
        public function getFragment();
        public function withScheme($scheme);
        public function withUserInfo($user, $password = null);
        public function withHost($host);
        public function withPort($port);
        public function withPath($path);
        public function withQuery($query);
        public function withFragment($fragment);
        public function __toString();
    }
}

namespace System\Http\Log {
    interface LoggerInterface
    {
        public function emergency($message, array $context = array());
        public function alert($message, array $context = array());
        public function critical($message, array $context = array());
        public function error($message, array $context = array());
        public function warning($message, array $context = array());
        public function notice($message, array $context = array());
        public function info($message, array $context = array());
        public function debug($message, array $context = array());
        public function log($level, $message, array $context = array());
    }
}

namespace System\Http\Promise {
    interface PromiseInterface
    {
        const PENDING = 'pending';
        const FULFILLED = 'fulfilled';
        const REJECTED = 'rejected';
        public function then(?callable $onFulfilled = null, ?callable $onRejected = null);
        public function otherwise(callable $onRejected);
        public function getState();
        public function resolve($value);
        public function reject($reason);
        public function wait($unwrap = true);
    }

    class Promise implements PromiseInterface
    {
        private $state = self::PENDING;
        private $result;
        private $waitFn;
        private $handlers = [];

        public function __construct(?callable $waitFn = null)
        {
            $this->waitFn = $waitFn;
        }

        public function then(?callable $onFulfilled = null, ?callable $onRejected = null)
        {
            $p = new Promise();
            if ($this->state === self::PENDING) {
                $this->handlers[] = [$p, $onFulfilled, $onRejected];
            } elseif ($this->state === self::FULFILLED) {
                $this->invoke($p, $this->result, true, $onFulfilled);
            } else {
                $this->invoke($p, $this->result, false, $onRejected);
            }
            return $p;
        }

        public function otherwise(callable $onRejected)
        {
            return $this->then(null, $onRejected);
        }

        public function getState()
        {
            return $this->state;
        }

        public function resolve($value)
        {
            if ($this->state !== self::PENDING) return;
            $this->state = self::FULFILLED;
            $this->result = $value;
            foreach ($this->handlers as $handler) {
                $this->invoke($handler[0], $value, true, $handler[1]);
            }
            $this->handlers = [];
        }

        public function reject($reason)
        {
            if ($this->state !== self::PENDING) return;
            $this->state = self::REJECTED;
            $this->result = $reason;
            foreach ($this->handlers as $handler) {
                $this->invoke($handler[0], $reason, false, $handler[2]);
            }
            $this->handlers = [];
        }

        public function wait($unwrap = true)
        {
            if ($this->state === self::PENDING && $this->waitFn) {
                call_user_func($this->waitFn, $this);
            }
            if ($this->state === self::REJECTED) {
                if ($this->result instanceof \Exception) throw $this->result;
                throw new \Exception((string)$this->result);
            }
            return $this->result;
        }

        private function invoke(Promise $p, $val, $isResolve, ?callable $fn = null)
        {
            if (!$fn) {
                if ($isResolve) $p->resolve($val);
                else $p->reject($val);
                return;
            }
            try {
                $res = $fn($val);
                if ($res instanceof PromiseInterface) {
                    $res->then([$p, 'resolve'], [$p, 'reject']);
                } else {
                    $p->resolve($res);
                }
            } catch (\Exception $e) {
                $p->reject($e);
            }
        }
    }

    class FulfilledPromise implements PromiseInterface
    {
        private $value;
        public function __construct($value)
        {
            $this->value = $value;
        }
        public function then(?callable $onFulfilled = null, ?callable $onRejected = null)
        {
            if (!$onFulfilled) return $this;
            $p = new Promise();
            try {
                $res = $onFulfilled($this->value);
                if ($res instanceof PromiseInterface) $res->then([$p, 'resolve'], [$p, 'reject']);
                else $p->resolve($res);
            } catch (\Exception $e) {
                $p->reject($e);
            }
            return $p;
        }
        public function otherwise(callable $onRejected)
        {
            return $this->then(null, $onRejected);
        }
        public function getState()
        {
            return self::FULFILLED;
        }
        public function resolve($value) {}
        public function reject($reason) {}
        public function wait($unwrap = true)
        {
            return $this->value;
        }
    }

    class RejectedPromise implements PromiseInterface
    {
        private $reason;
        public function __construct($reason)
        {
            $this->reason = $reason;
        }
        public function then(?callable $onFulfilled = null, ?callable $onRejected = null)
        {
            if (!$onRejected) return $this;
            $p = new Promise();
            try {
                $res = $onRejected($this->reason);
                if ($res instanceof PromiseInterface) $res->then([$p, 'resolve'], [$p, 'reject']);
                else $p->resolve($res);
            } catch (\Exception $e) {
                $p->reject($e);
            }
            return $p;
        }
        public function otherwise(callable $onRejected)
        {
            return $this->then(null, $onRejected);
        }
        public function getState()
        {
            return self::REJECTED;
        }
        public function resolve($value) {}
        public function reject($reason) {}
        public function wait($unwrap = true)
        {
            if ($this->reason instanceof \Exception) throw $this->reason;
            throw new \Exception((string)$this->reason);
        }
    }

    function rejection_for($reason)
    {
        if ($reason instanceof PromiseInterface) return $reason;
        return new RejectedPromise($reason);
    }
}

namespace System\Http\Exception {
    interface GuzzleException {}
    class TransferException extends \RuntimeException implements GuzzleException {}
    class RequestException extends TransferException
    {
        private $request;
        private $response;
        public function __construct($message, \System\Http\Message\RequestInterface $request, ?\System\Http\Message\ResponseInterface $response = null, ?\Exception $previous = null)
        {
            parent::__construct($message, 0, $previous);
            $this->request = $request;
            $this->response = $response;
        }
        public function getRequest()
        {
            return $this->request;
        }
        public function getResponse()
        {
            return $this->response;
        }
    }
    class ConnectException extends RequestException {}
    class BadResponseException extends RequestException {}
    class ServerException extends BadResponseException {}
    class ClientException extends BadResponseException {}
    class TooManyRedirectsException extends RequestException {}
}

namespace System\Http\Psr7 {

    use System\Http\Message\StreamInterface;
    use System\Http\Message\UriInterface;
    use System\Http\Message\MessageInterface;
    use System\Http\Message\RequestInterface;
    use System\Http\Message\ResponseInterface;

    class Stream implements StreamInterface
    {
        private $stream;
        public function __construct($stream)
        {
            $this->stream = $stream;
        }
        public function __toString()
        {
            try {
                $this->rewind();
                return $this->getContents();
            } catch (\Exception $e) {
                return '';
            }
        }
        public function close()
        {
            if (is_resource($this->stream)) fclose($this->stream);
            $this->detach();
        }
        public function detach()
        {
            $result = $this->stream;
            $this->stream = null;
            return $result;
        }
        public function getSize()
        {
            if (!is_resource($this->stream)) return null;
            $stats = fstat($this->stream);
            return $stats['size'] ?? null;
        }
        public function tell()
        {
            return ftell($this->stream);
        }
        public function eof()
        {
            return !$this->stream || feof($this->stream);
        }
        public function isSeekable()
        {
            return $this->getMetadata('seekable') ?? false;
        }
        public function seek($offset, $whence = SEEK_SET)
        {
            fseek($this->stream, $offset, $whence);
        }
        public function rewind()
        {
            $this->seek(0);
        }
        public function isWritable()
        {
            return true;
        }
        public function write($string)
        {
            return fwrite($this->stream, $string);
        }
        public function isReadable()
        {
            return true;
        }
        public function read($length)
        {
            return fread($this->stream, $length);
        }
        public function getContents()
        {
            return stream_get_contents($this->stream);
        }
        public function getMetadata($key = null)
        {
            if (!is_resource($this->stream)) return $key ? null : [];
            $meta = stream_get_meta_data($this->stream);
            if (!$key) return $meta;
            return $meta[$key] ?? null;
        }
        public static function factory($content = '')
        {
            $stream = fopen('php://temp', 'r+');
            if ($content !== '') {
                fwrite($stream, $content);
                rewind($stream);
            }
            return new self($stream);
        }
    }

    class Uri implements UriInterface
    {
        private $scheme = '';
        private $userInfo = '';
        private $host = '';
        private $port;
        private $path = '';
        private $query = '';
        private $fragment = '';

        public function __construct($uri = '')
        {
            if ($uri !== '') {
                $parts = parse_url($uri);
                $this->scheme = $parts['scheme'] ?? '';
                $this->userInfo = ($parts['user'] ?? '') . (isset($parts['pass']) ? ':' . $parts['pass'] : '');
                $this->host = $parts['host'] ?? '';
                $this->port = $parts['port'] ?? null;
                $this->path = $parts['path'] ?? '';
                $this->query = $parts['query'] ?? '';
                $this->fragment = $parts['fragment'] ?? '';
            }
        }
        public function getScheme()
        {
            return $this->scheme;
        }
        public function getAuthority()
        {
            $auth = $this->host;
            if ($this->userInfo !== '') $auth = $this->userInfo . '@' . $auth;
            if ($this->port !== null) $auth .= ':' . $this->port;
            return $auth;
        }
        public function getUserInfo()
        {
            return $this->userInfo;
        }
        public function getHost()
        {
            return $this->host;
        }
        public function getPort()
        {
            return $this->port;
        }
        public function getPath()
        {
            return $this->path;
        }
        public function getQuery()
        {
            return $this->query;
        }
        public function getFragment()
        {
            return $this->fragment;
        }
        public function withScheme($scheme)
        {
            $u = clone $this;
            $u->scheme = $scheme;
            return $u;
        }
        public function withUserInfo($user, $password = null)
        {
            $u = clone $this;
            $u->userInfo = $user . ($password ? ':' . $password : '');
            return $u;
        }
        public function withHost($host)
        {
            $u = clone $this;
            $u->host = $host;
            return $u;
        }
        public function withPort($port)
        {
            $u = clone $this;
            $u->port = $port;
            return $u;
        }
        public function withPath($path)
        {
            $u = clone $this;
            $u->path = $path;
            return $u;
        }
        public function withQuery($query)
        {
            $u = clone $this;
            $u->query = $query;
            return $u;
        }
        public function withFragment($fragment)
        {
            $u = clone $this;
            $u->fragment = $fragment;
            return $u;
        }
        public function __toString()
        {
            $uri = '';
            if ($this->scheme !== '') $uri .= $this->scheme . '://';
            if ($this->getAuthority() !== '') $uri .= $this->getAuthority();
            if ($this->path !== '' && $this->path[0] !== '/' && $uri !== '') {
                $uri .= '/';
            }
            $uri .= $this->path;
            if ($this->query !== '') $uri .= '?' . $this->query;
            if ($this->fragment !== '') $uri .= '#' . $this->fragment;
            return $uri;
        }
    }

    class Message implements MessageInterface
    {
        protected $headers = [];
        protected $headerNames = [];
        protected $protocol = '1.1';
        protected $stream;

        public function getProtocolVersion()
        {
            return $this->protocol;
        }
        public function withProtocolVersion($version)
        {
            $m = clone $this;
            $m->protocol = $version;
            return $m;
        }
        public function getHeaders()
        {
            return $this->headers;
        }
        public function hasHeader($name)
        {
            return isset($this->headerNames[strtolower($name)]);
        }
        public function getHeader($name)
        {
            $name = strtolower($name);
            if (!isset($this->headerNames[$name])) return [];
            return $this->headers[$this->headerNames[$name]];
        }
        public function getHeaderLine($name)
        {
            return implode(', ', $this->getHeader($name));
        }
        public function withHeader($name, $value)
        {
            $m = clone $this;
            $normalized = strtolower($name);
            if (isset($m->headerNames[$normalized])) {
                unset($m->headers[$m->headerNames[$normalized]]);
            }
            $m->headerNames[$normalized] = $name;
            $m->headers[$name] = is_array($value) ? $value : [$value];
            return $m;
        }
        public function withAddedHeader($name, $value)
        {
            $m = clone $this;
            $normalized = strtolower($name);
            if (!isset($m->headerNames[$normalized])) {
                $m->headerNames[$normalized] = $name;
                $m->headers[$name] = is_array($value) ? $value : [$value];
            } else {
                $m->headers[$m->headerNames[$normalized]] = array_merge($m->headers[$m->headerNames[$normalized]], is_array($value) ? $value : [$value]);
            }
            return $m;
        }
        public function withoutHeader($name)
        {
            $m = clone $this;
            $normalized = strtolower($name);
            if (isset($m->headerNames[$normalized])) {
                unset($m->headers[$m->headerNames[$normalized]]);
                unset($m->headerNames[$normalized]);
            }
            return $m;
        }
        public function getBody()
        {
            if (!$this->stream) $this->stream = Stream::factory('');
            return $this->stream;
        }
        public function withBody(StreamInterface $body)
        {
            $m = clone $this;
            $m->stream = $body;
            return $m;
        }
    }

    class Request extends Message implements RequestInterface
    {
        private $method;
        private $requestTarget;
        private $uri;

        public function __construct($method, $uri, array $headers = [], $body = null, $version = '1.1')
        {
            $this->method = $method;
            $this->uri = $uri instanceof UriInterface ? $uri : new Uri($uri);
            foreach ($headers as $k => $v) {
                $this->headers[$k] = is_array($v) ? $v : [$v];
                $this->headerNames[strtolower($k)] = $k;
            }
            if ($body !== null) $this->stream = $body instanceof StreamInterface ? $body : Stream::factory($body);
            $this->protocol = $version;
            if (!$this->hasHeader('Host') && $this->uri->getHost() !== '') {
                $this->headerNames['host'] = 'Host';
                $this->headers['Host'] = [$this->uri->getHost()];
            }
        }
        public function getRequestTarget()
        {
            if ($this->requestTarget !== null) return $this->requestTarget;
            $target = $this->uri->getPath();
            if ($target == '') $target = '/';
            if ($this->uri->getQuery() != '') $target .= '?' . $this->uri->getQuery();
            return $target;
        }
        public function withRequestTarget($requestTarget)
        {
            $r = clone $this;
            $r->requestTarget = $requestTarget;
            return $r;
        }
        public function getMethod()
        {
            return $this->method;
        }
        public function withMethod($method)
        {
            $r = clone $this;
            $r->method = $method;
            return $r;
        }
        public function getUri()
        {
            return $this->uri;
        }
        public function withUri(UriInterface $uri, $preserveHost = false)
        {
            $r = clone $this;
            $r->uri = $uri;
            if (!$preserveHost || !$r->hasHeader('Host')) {
                if ($uri->getHost() !== '') {
                    $r->headerNames['host'] = 'Host';
                    $r->headers['Host'] = [$uri->getHost()];
                }
            }
            return $r;
        }
    }

    class Response extends Message implements ResponseInterface
    {
        private $statusCode;
        private $reasonPhrase;

        public function __construct($status = 200, array $headers = [], $body = null, $version = '1.1', $reason = null)
        {
            $this->statusCode = (int)$status;
            if ($body !== null) $this->stream = $body instanceof StreamInterface ? $body : Stream::factory($body);
            foreach ($headers as $k => $v) {
                $this->headers[$k] = is_array($v) ? $v : [$v];
                $this->headerNames[strtolower($k)] = $k;
            }
            $this->protocol = $version;
            $this->reasonPhrase = $reason ?: '';
        }
        public function getStatusCode()
        {
            return $this->statusCode;
        }
        public function withStatus($code, $reasonPhrase = '')
        {
            $r = clone $this;
            $r->statusCode = $code;
            $r->reasonPhrase = $reasonPhrase;
            return $r;
        }
        public function getReasonPhrase()
        {
            return $this->reasonPhrase;
        }
    }
}

namespace System\Http\Handler {

    use System\Http\Promise\Promise;
    use System\Http\Message\RequestInterface;
    use System\Http\Psr7\Response;
    use System\Http\Exception\ConnectException;

    class CurlHandler
    {
        public function __invoke(RequestInterface $request, array $options)
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, (string)$request->getUri());

            // Do not use CURLOPT_RETURNTRANSFER when sink is used if we want to stream directly.
            // But we also need headers. The best way is to use CURLOPT_HEADERFUNCTION.
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);

            $responseHeaders = [];
            $responseStatus = 200;
            $responseReason = '';

            curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($curl, $header_line) use (&$responseHeaders, &$responseStatus, &$responseReason) {
                if (strpos($header_line, 'HTTP/') === 0) {
                    preg_match('#^HTTP/\d\.\d\s+(\d+)\s*(.*)$#', $header_line, $m);
                    $responseStatus = isset($m[1]) ? (int)$m[1] : 200;
                    $responseReason = trim($m[2] ?? '');
                } elseif (strpos($header_line, ':') !== false) {
                    list($k, $v) = explode(':', $header_line, 2);
                    $responseHeaders[trim($k)][] = trim($v);
                }
                return strlen($header_line);
            });

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request->getMethod());

            $headers = [];
            foreach ($request->getHeaders() as $name => $values) {
                foreach ($values as $value) {
                    $headers[] = "$name: $value";
                }
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $body = (string)$request->getBody();
            if ($body !== '') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            }

            if (isset($options['timeout'])) curl_setopt($ch, CURLOPT_TIMEOUT, $options['timeout']);
            if (isset($options['connect_timeout'])) curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $options['connect_timeout']);
            if (isset($options['proxy'])) curl_setopt($ch, CURLOPT_PROXY, $options['proxy']);
            if (isset($options['debug']) && $options['debug']) {
                curl_setopt($ch, CURLOPT_VERBOSE, true);
            }
            if (isset($options['verify']) && !$options['verify']) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            }

            if (isset($options['auth'])) {
                if (is_array($options['auth'])) {
                    $user = $options['auth'][0] ?? '';
                    $pass = $options['auth'][1] ?? '';
                    $type = isset($options['auth'][2]) ? strtolower($options['auth'][2]) : 'basic';

                    curl_setopt($ch, CURLOPT_USERPWD, "$user:$pass");

                    if ($type === 'digest') {
                        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
                    } elseif ($type === 'ntlm') {
                        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
                    } elseif ($type === 'any') {
                        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
                    } else {
                        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                    }
                } elseif (is_string($options['auth'])) {
                    $headers[] = "Authorization: Bearer " . $options['auth'];
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                }
            }

            $sink = null;
            $bodyBuffer = fopen('php://temp', 'w+');

            if (isset($options['sink'])) {
                if (is_string($options['sink'])) {
                    $sink = fopen($options['sink'], 'w+');
                } elseif (is_resource($options['sink'])) {
                    $sink = $options['sink'];
                } elseif ($options['sink'] instanceof \System\Http\Message\StreamInterface) {
                    $sink = fopen('php://temp', 'w+');
                }

                if ($sink) {
                    curl_setopt($ch, CURLOPT_FILE, $sink);
                }
            } else {
                curl_setopt($ch, CURLOPT_FILE, $bodyBuffer);
            }

            $promise = new Promise(function ($promise) use ($ch, $request, $options, $sink, $bodyBuffer, &$responseHeaders, &$responseStatus, &$responseReason) {
                $responseContent = curl_exec($ch);
                if ($responseContent === false) {
                    if ($sink && is_string($options['sink'] ?? null)) fclose($sink);
                    fclose($bodyBuffer);
                    $promise->reject(new ConnectException(curl_error($ch), $request));
                    curl_close($ch);
                    return;
                }

                $responseBodyStream = $sink ? $sink : $bodyBuffer;
                rewind($responseBodyStream);

                $responseStream = new \System\Http\Psr7\Stream($responseBodyStream);

                $response = new Response($responseStatus, $responseHeaders, $responseStream, '1.1', $responseReason);

                if (isset($options['http_errors']) && $options['http_errors']) {
                    if ($responseStatus >= 400 && $responseStatus < 500) {
                        $promise->reject(new \System\Http\Exception\ClientException("Client error", $request, $response));
                    } elseif ($responseStatus >= 500) {
                        $promise->reject(new \System\Http\Exception\ServerException("Server error", $request, $response));
                    } else {
                        $promise->resolve($response);
                    }
                } else {
                    $promise->resolve($response);
                }
                curl_close($ch);
            });
            return $promise;
        }
    }

    class MockHandler
    {
        private $queue;
        public function __construct(array $queue = [])
        {
            $this->queue = $queue;
        }
        public function append($response)
        {
            $this->queue[] = $response;
        }
        public function __invoke(RequestInterface $request, array $options)
        {
            $response = array_shift($this->queue);
            $promise = new Promise();
            if ($response instanceof \Exception) {
                $promise->reject($response);
            } else {
                if (isset($options['sink'])) {
                    $sink = $options['sink'];
                    if (is_string($sink)) {
                        file_put_contents($sink, (string)$response->getBody());
                        $response = clone $response;
                        $response = $response->withBody(new \System\Http\Psr7\Stream(fopen($sink, 'r')));
                    } elseif (is_resource($sink)) {
                        fwrite($sink, (string)$response->getBody());
                        rewind($sink);
                        $response = clone $response;
                        $response = $response->withBody(new \System\Http\Psr7\Stream($sink));
                    }
                }

                if (isset($options['http_errors']) && $options['http_errors']) {
                    $statusCode = $response->getStatusCode();
                    if ($statusCode >= 400 && $statusCode < 500) {
                        $promise->reject(new \System\Http\Exception\ClientException("Client error", $request, $response));
                    } elseif ($statusCode >= 500) {
                        $promise->reject(new \System\Http\Exception\ServerException("Server error", $request, $response));
                    } else {
                        $promise->resolve($response);
                    }
                } else {
                    $promise->resolve($response);
                }
            }
            return $promise;
        }
    }
}

namespace System {

    use System\Http\Message\RequestInterface;
    use System\Http\Message\ResponseInterface;
    use System\Http\Handler\CurlHandler;

    class HandlerStack
    {
        private $handler;
        private $stack = [];
        public function __construct(?callable $handler = null)
        {
            $this->handler = $handler ?: new CurlHandler();
        }
        public static function create(?callable $handler = null)
        {
            return new self($handler);
        }
        public function push(callable $middleware, $name = '')
        {
            $this->stack[] = [$middleware, $name];
        }
        public function resolve()
        {
            $prev = $this->handler;
            foreach (array_reverse($this->stack) as $m) {
                $prev = $m[0]($prev);
            }
            return $prev;
        }
        public function __invoke(RequestInterface $request, array $options)
        {
            $handler = $this->resolve();
            return $handler($request, $options);
        }
    }

    class Middleware
    {
        public static function history(&$container)
        {
            return function (callable $handler) use (&$container) {
                return function (RequestInterface $request, array $options) use ($handler, &$container) {
                    return $handler($request, $options)->then(
                        function ($response) use ($request, $options, &$container) {
                            $container[] = ['request' => $request, 'response' => $response, 'error' => null, 'options' => $options];
                            return $response;
                        },
                        function ($reason) use ($request, $options, &$container) {
                            $container[] = ['request' => $request, 'response' => null, 'error' => $reason, 'options' => $options];
                            return \System\Http\Promise\rejection_for($reason);
                        }
                    );
                };
            };
        }
    }

    /**
     * Client HTTP principale (System)
     * 
     * Esempi di utilizzo:
     * 
     * 1. Richiesta GET semplice
     *    $client = new \System\CurlClient();
     *    $response = $client->get('https://service.example.com/users');
     *    echo $response->getBody();
     * 
     * 2. Richiesta POST con dati JSON
     *    $response = $client->post('https://service.example.com/users', [
     *        'json' => ['nome' => 'Mario', 'cognome' => 'Rossi']
     *    ]);
     * 
     * 3. Download di un file di grandi dimensioni su disco (senza saturare la RAM)
     *    $client->get('https://example.com/file-gigante.zip', [
     *        'sink' => '/percorso/locale/salvataggio.zip'
     *    ]);
     * 
     * 4. Autenticazione (Basic, Digest, Bearer token)
     *    $client->get('https://service.example.com/protected', [
     *        'auth' => ['utente', 'password'] // Basic auth
     *        // 'auth' => 'mio-token-segreto' // Bearer token
     *    ]);
     * 5. Disabilitare la verifica SSL (per ambienti di test locale)
     *    $client = new \System\CurlClient(['verify' => false]);
     *    $client->get('https://localhost/service');
     */
    class CurlClient
    {
        /** @var array Configurazioni globali del client */
        private $config;

        /**
         * Costruttore del Client HTTP.
         * 
         * @param array $config Opzioni di default per tutte le richieste (es. base_uri, timeout, headers).
         */
        public function __construct(array $config = [])
        {
            if (!isset($config['handler'])) {
                $config['handler'] = HandlerStack::create();
            }
            if (!isset($config['http_errors'])) {
                $config['http_errors'] = true;
            }
            $this->config = $config;
        }

        /**
         * Disabilita temporaneamente la verifica del certificato SSL per tutte le richieste successive.
         * (Equivalente all'opzione globale ['verify' => false]).
         * Utile per ambienti di sviluppo locale o certificati self-signed.
         * 
         * @return self
         */
        public function disableSslVerification()
        {
            $this->config['verify'] = false;
            return $this;
        }

        /**
         * Esegue una richiesta HTTP sincrona.
         * 
         * @param string $method Metodo HTTP (GET, POST, PUT, ecc.)
         * @param string $uri URI di destinazione
         * @param array $options Opzioni della richiesta (json, query, form_params, multipart, auth, ecc.)
         * @return ResponseInterface Risposta conforme allo standard PSR-7
         */
        public function request($method, $uri = '', array $options = [])
        {
            return $this->requestAsync($method, $uri, $options)->wait();
        }

        /**
         * Esegue una richiesta HTTP asincrona (restituisce una Promise).
         * 
         * @param string $method Metodo HTTP
         * @param string $uri URI di destinazione
         * @param array $options Opzioni della richiesta
         * @return \System\Http\Promise\PromiseInterface Promise da risolvere con ->wait()
         */
        public function requestAsync($method, $uri = '', array $options = [])
        {
            $options = array_merge($this->config, $options);

            $baseUri = $options['base_uri'] ?? null;
            $uriObj = new Http\Psr7\Uri($uri);
            if ($baseUri) {
                $baseUriObj = new Http\Psr7\Uri($baseUri);
                $uriObj = $baseUriObj->withPath(rtrim($baseUriObj->getPath(), '/') . '/' . ltrim($uriObj->getPath(), '/'))
                    ->withQuery($uriObj->getQuery());
            }

            $request = new Http\Psr7\Request($method, $uriObj);

            if (isset($options['query'])) {
                $query = is_array($options['query']) ? http_build_query($options['query'], '', '&', PHP_QUERY_RFC3986) : $options['query'];
                $existingQuery = $request->getUri()->getQuery();
                if ($existingQuery !== '') {
                    $query = $existingQuery . '&' . $query;
                }
                $request = $request->withUri($request->getUri()->withQuery($query));
            }
            if (isset($options['headers'])) {
                foreach ($options['headers'] as $k => $v) {
                    $request = $request->withHeader($k, $v);
                }
            }
            if (isset($options['json'])) {
                $request = $request->withHeader('Content-Type', 'application/json');
                $request = $request->withBody(Http\Psr7\Stream::factory(json_encode($options['json'])));
            } elseif (isset($options['form_params'])) {
                $request = $request->withHeader('Content-Type', 'application/x-www-form-urlencoded');
                $request = $request->withBody(Http\Psr7\Stream::factory(http_build_query($options['form_params'])));
            } elseif (isset($options['multipart'])) {
                $boundary = uniqid('----FormBoundary');
                $request = $request->withHeader('Content-Type', 'multipart/form-data; boundary=' . $boundary);
                $body = '';
                foreach ($options['multipart'] as $field) {
                    $body .= "--{$boundary}\r\n";
                    $body .= "Content-Disposition: form-data; name=\"{$field['name']}\"";
                    if (isset($field['filename'])) {
                        $body .= "; filename=\"{$field['filename']}\"";
                    }
                    $body .= "\r\n";
                    if (isset($field['headers'])) {
                        foreach ($field['headers'] as $k => $v) {
                            $body .= "{$k}: {$v}\r\n";
                        }
                    }
                    $body .= "\r\n" . $field['contents'] . "\r\n";
                }
                $body .= "--{$boundary}--\r\n";
                $request = $request->withBody(Http\Psr7\Stream::factory($body));
            } elseif (isset($options['body'])) {
                $request = $request->withBody(Http\Psr7\Stream::factory($options['body']));
            }

            $handler = $options['handler'];
            return $handler($request, $options);
        }

        /**
         * Recupera una singola opzione o l'intero array di configurazione globale del client.
         * 
         * @param string|null $option Nome dell'opzione da cercare (es. 'base_uri')
         * @return mixed Valore dell'opzione o intero array se null
         */
        public function getConfig($option = null)
        {
            return $option === null ? $this->config : ($this->config[$option] ?? null);
        }

        /** Shortcut per richieste GET */
        public function get($uri, array $options = [])
        {
            return $this->request('GET', $uri, $options);
        }
        /** Shortcut per richieste POST */
        public function post($uri, array $options = [])
        {
            return $this->request('POST', $uri, $options);
        }
        /** Shortcut per richieste PUT */
        public function put($uri, array $options = [])
        {
            return $this->request('PUT', $uri, $options);
        }
        /** Shortcut per richieste PATCH */
        public function patch($uri, array $options = [])
        {
            return $this->request('PATCH', $uri, $options);
        }
        /** Shortcut per richieste DELETE */
        public function delete($uri, array $options = [])
        {
            return $this->request('DELETE', $uri, $options);
        }
        /** Shortcut per richieste HEAD */
        public function head($uri, array $options = [])
        {
            return $this->request('HEAD', $uri, $options);
        }
        /** Shortcut per richieste OPTIONS */
        public function options($uri, array $options = [])
        {
            return $this->request('OPTIONS', $uri, $options);
        }

    }
}
