# RelativeToAbsoluteUrl

This script converts the relative url to absolute url, provided a base url.


For more, look here: http://publicmind.in/blog/urltoabsolute

## Usage:

### First step - add library to project

#### Composer (.json)
````
{
    "require": {
        "oldmine/relative-to-absolute-url": "master"
    }
}
````

#### Composer (Command Line)
````
composer require oldmine/relative-to-absolute-url
````

#### Require:
````
require_once('src/RelativeToAbsoluteUrl.php');
````

### Second step - call static method urlToAbsolute() in class RelativeToAbsoluteUrl
````
$absoluteUrl = RelativeToAbsoluteUrl::urlToAbsolute('http://test.com/dir1/dir2/', 'test')
````

## Author/credits

1) Original author: David R. Nadeau, NadeauSoftware.com
2) Edited and maintained by: Nitin Kr, Gupta, publicmind.in
3) Edited and maintained by: Daniil Zhelninskiy <webmailexec@gmail.com>

## Changelog
[CHANGELOG](CHANGELOG.md)