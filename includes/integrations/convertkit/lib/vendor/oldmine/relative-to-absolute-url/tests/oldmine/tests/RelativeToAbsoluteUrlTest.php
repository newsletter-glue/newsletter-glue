<?php
/**
 * User: Daniil Zhelninskiy <webmailexec@gmail.com>
 * Date: 23.12.2018
 */

namespace oldmine\RelativeToAbsoluteUrl\Tests;

use oldmine\RelativeToAbsoluteUrl\RelativeToAbsoluteUrl;
use PHPUnit\Framework\TestCase;

class RelativeToAbsoluteUrlTest extends TestCase
{

    public function testUrlToAbsoluteGoodUrls()
    {
        $this->assertNotEmpty(RelativeToAbsoluteUrl::urlToAbsolute('http://test.com/dir1/page', 'test'));
        $this->assertNotEmpty(RelativeToAbsoluteUrl::urlToAbsolute('http://test.com/dir1/dir2/', 'test'));
        $this->assertNotEmpty(RelativeToAbsoluteUrl::urlToAbsolute('http://test.com/dir1/page', '/test'));
        $this->assertNotEmpty(RelativeToAbsoluteUrl::urlToAbsolute('http://test.com/dir1/page', '//test'));
        $this->assertNotEmpty(RelativeToAbsoluteUrl::urlToAbsolute('http://test.com/dir1/page', './test'));
        $this->assertNotEmpty(RelativeToAbsoluteUrl::urlToAbsolute('http://test.com', 'test'));
        $this->assertNotEmpty(RelativeToAbsoluteUrl::urlToAbsolute('http://test.com/', 'test'));
    }

    public function testUrlToAbsoluteBadUrls()
    {
        $this->assertEmpty(RelativeToAbsoluteUrl::urlToAbsolute('http://test.com/dir1/page', '[test'));
    }
}
