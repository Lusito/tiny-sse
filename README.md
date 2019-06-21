# TinySSE

[![License](https://img.shields.io/badge/License-zlib/libpng-blue.svg)](https://github.com/Lusito/tiny-sse/blob/master/LICENSE)

TinySSE is small helper to publish [Server Sent Events](https://en.wikipedia.org/wiki/Server-sent_events) aka EventSource.

### Why TinySSE?

- It's minimalistic.
- It packs everything you need:
  - Send all necessary headers.
  - To send events and comments to the client (browser)
  - Detect disconnects.
- TinySSE is released under the liberal zlib/png license.

**Fair warning:** PHP is not a good language for SSE or any other push-style communication, since it will use one thread per request. So you should keep the use at a minimum (like for a limited number of users).

### Example

A simple example:

```php

use Lusito\TinySSE;

$lastState = null;
$sse = new TinySSE();

do {
    $state = getState();
    if(statesDiffer($state, $lastState)) {
        $sse->sendEvent(json_encode($state, JSON_UNESCAPED_SLASHES), 'update');
        $lastState = $state;
    }
} while($sse->sleep());
```

### Setup

Install via composer:

`composer require lusito/tiny-sse`


Include the autoloader in your php script, unless you've done that already:

```php
require __DIR__ . '/vendor/autoload.php';
```

### Documentation

#### Constructor

The constructor takes one optional argument: The number of frames (sleep calls) to count until a comment is flushed to keep the connection alive (int, defaults to 10).

After sleep() has been called this many times without sending anything, a 'noop' comment will be send.

The following headers will be set in the constructor:
- Content-Type: text/event-stream
- Cache-Control: no-cache
- Connection: keep-alive
- X-Accel-Buffering: no

#### sleep()

The sleep() method takes two optional parameters:

- The number of seconds to sleep (number, defaults to 1)
- The time limit in seconds. 

If you set ignore_user_abort to true and the connection has been disconnected, this will return false. Otherwise, it will call set_time_limit() with the specified time limit, sleep the specified number of seconds and then return true.

#### sendComment()

This takes a single string parameter to send as comment. This can contain linebreaks.

The comment will be written to output and flushed.

#### sendEvent()

This takes 3 parameters:
- A string parameter to send as data. This can contain linebreaks.
- An optional event name. No newline is allowed.
- An optional id. No newline is allowed.

This event will be written to output and flushed.

### Report isssues

Something not working quite as expected? Do you need a feature that has not been implemented yet? Check the [issue tracker](https://github.com/Lusito/tiny-sse/issues) and add a new one if your problem is not already listed. Please try to provide a detailed description of your problem, including the steps to reproduce it.

### Contribute

Awesome! If you would like to contribute with a new feature or submit a bugfix, fork this repo and send a pull request. Please, make sure all the unit tests are passing before submitting and add new ones in case you introduced new features.

### License

tiny-sse has been released under the [zlib/libpng](https://github.com/Lusito/tiny-sse/blob/master/LICENSE) license, meaning you
can use it free of charge, without strings attached in commercial and non-commercial projects. Credits are appreciated but not mandatory.
