<?php

/**
 * Edited by Daniil Zhelninskiy <webmailexec@gmail.com>
 * Date: 23.12.2018
 * Version 1.5
 */

/**
 * Edited by Nitin Kr. Gupta, publicmind.in
 */

/**
 * Copyright (c) 2008, David R. Nadeau, NadeauSoftware.com.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *    * Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 *
 *    * Redistributions in binary form must reproduce the above
 *      copyright notice, this list of conditions and the following
 *      disclaimer in the documentation and/or other materials provided
 *      with the distribution.
 *
 *    * Neither the names of David R. Nadeau or NadeauSoftware.com, nor
 *      the names of its contributors may be used to endorse or promote
 *      products derived from this software without specific prior
 *      written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY
 * WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY
 * OF SUCH DAMAGE.
 */

/*
 * This is a BSD License approved by the Open Source Initiative (OSI).
 * See:  http://www.opensource.org/licenses/bsd-license.php
 */

namespace oldmine\RelativeToAbsoluteUrl;

class RelativeToAbsoluteUrl
{
    /**
     * Combine a base URL and a relative URL to produce a new
     * absolute URL.  The base URL is often the URL of a page,
     * and the relative URL is a URL embedded on that page.
     *
     * This function implements the "absolutize" algorithm from
     * the RFC3986 specification for URLs.
     *
     * This function supports multi-byte characters with the UTF-8 encoding,
     * per the URL specification.
     *
     * @param $baseUrl - the absolute base URL.
     * @param $relativeUrl - the relative URL to convert.
     *
     * @return bool|string An absolute URL that combines parts of the base and relative
     *    URLs, or empty string if the base URL is not absolute or if either
     *    URL cannot be parsed.
     */
    public static function urlToAbsolute($baseUrl, $relativeUrl)
    {
        // If relative URL has a scheme, clean path and return.
        $r = self::splitUrl($relativeUrl);
        if ($r === false)
            return '';
        if (!empty($r['scheme'])) {
            if (!empty($r['path']) && $r['path'][0] == '/')
                $r['path'] = self::urlRemoveDotSegments($r['path']);
            return self::joinUrl($r);
        }

        // Make sure the base URL is absolute.
        $b = self::splitUrl($baseUrl);
        if ($b === false || empty($b['scheme']) || empty($b['host']))
            return '';
        $r['scheme'] = $b['scheme'];

        // If relative URL has an authority, clean path and return.
        if (isset($r['host'])) {
            if (!empty($r['path']))
                $r['path'] = self::urlRemoveDotSegments($r['path']);
            return self::joinUrl($r);
        }
        unset($r['port']);
        unset($r['user']);
        unset($r['pass']);

        // Copy base authority.
        $r['host'] = $b['host'];
        if (isset($b['port'])) $r['port'] = $b['port'];
        if (isset($b['user'])) $r['user'] = $b['user'];
        if (isset($b['pass'])) $r['pass'] = $b['pass'];

        // If relative URL has no path, use base path
        if (empty($r['path'])) {
            if (!empty($b['path']))
                $r['path'] = $b['path'];
            if (!isset($r['query']) && isset($b['query']))
                $r['query'] = $b['query'];
            return self::joinUrl($r);
        }

        // If relative URL path doesn't start with /, merge with base path
        if ($r['path'][0] != '/') {
            $base = mb_strrchr($b['path'], '/', true, 'UTF-8');
            if ($base === false) $base = '';
            $r['path'] = $base . '/' . $r['path'];
        }
        $r['path'] = self::urlRemoveDotSegments($r['path']);
        return self::joinUrl($r);
    }

    /**
     * This function parses an absolute or relative URL and splits it
     * into individual components.
     *
     * RFC3986 specifies the components of a Uniform Resource Identifier (URI).
     * A portion of the ABNFs are repeated here:
     *
     *    URI-reference    = URI
     *            / relative-ref
     *
     *    URI        = scheme ":" hier-part [ "?" query ] [ "#" fragment ]
     *
     *    relative-ref    = relative-part [ "?" query ] [ "#" fragment ]
     *
     *    hier-part    = "//" authority path-abempty
     *            / path-absolute
     *            / path-rootless
     *            / path-empty
     *
     *    relative-part    = "//" authority path-abempty
     *            / path-absolute
     *            / path-noscheme
     *            / path-empty
     *
     *    authority    = [ userinfo "@" ] host [ ":" port ]
     *
     * So, a URL has the following major components:
     *
     *    scheme
     *        The name of a method used to interpret the rest of
     *        the URL.  Examples:  "http", "https", "mailto", "file'.
     *
     *    authority
     *        The name of the authority governing the URL's name
     *        space.  Examples:  "example.com", "user@example.com",
     *        "example.com:80", "user:password@example.com:80".
     *
     *        The authority may include a host name, port number,
     *        user name, and password.
     *
     *        The host may be a name, an IPv4 numeric address, or
     *        an IPv6 numeric address.
     *
     *    path
     *        The hierarchical path to the URL's resource.
     *        Examples:  "/index.htm", "/scripts/page.php".
     *
     *    query
     *        The data for a query.  Examples:  "?search=google.com".
     *
     *    fragment
     *        The name of a secondary resource relative to that named
     *        by the path.  Examples:  "#section1", "#header".
     *
     * An "absolute" URL must include a scheme and path.  The authority, query,
     * and fragment components are optional.
     *
     * A "relative" URL does not include a scheme and must include a path.  The
     * authority, query, and fragment components are optional.
     *
     * This function splits the $url argument into the following components
     * and returns them in an associative array.  Keys to that array include:
     *
     *    "scheme"    The scheme, such as "http".
     *    "host"        The host name, IPv4, or IPv6 address.
     *    "port"        The port number.
     *    "user"        The user name.
     *    "pass"        The user password.
     *    "path"        The path, such as a file path for "http".
     *    "query"        The query.
     *    "fragment"    The fragment.
     *
     * One or more of these may not be present, depending upon the URL.
     *
     * Optionally, the "user", "pass", "host" (if a name, not an IP address),
     * "path", "query", and "fragment" may have percent-encoded characters
     * decoded.  The "scheme" and "port" cannot include percent-encoded
     * characters and are never decoded.  Decoding occurs after the URL has
     * been parsed.
     *
     * @param $url - the URL to parse.
     * @param bool $decode - an optional boolean flag selecting whether
     *            to decode percent encoding or not.  Default = TRUE.
     *
     * @return mixed The associative array of URL parts, or FALSE if the URL is
     *    too malformed to recognize any parts.
     */
    private static function splitUrl($url, $decode = false)
    {
        // Character sets from RFC3986.
        $xunressub = 'a-zA-Z\d\-._~\!$&\'()*+,;=';
        $xpchar = $xunressub . ':@% ';

        // Scheme from RFC3986.
        $xscheme = '([a-zA-Z][a-zA-Z\d+-.]*)';

        // User info (user + password) from RFC3986.
        $xuserinfo = '(([' . $xunressub . '%]*)' .
            '(:([' . $xunressub . ':%]*))?)';

        // IPv4 from RFC3986 (without digit constraints).
        $xipv4 = '(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})';

        // IPv6 from RFC2732 (without digit and grouping constraints).
        $xipv6 = '(\[([a-fA-F\d.:]+)\])';

        // Host name from RFC1035.  Technically, must start with a letter.
        // Relax that restriction to better parse URL structure, then
        // leave host name validation to application.
        $xhost_name = '([a-zA-Z\d\-.%]+)';

        // Authority from RFC3986.  Skip IP future.
        $xhost = '(' . $xhost_name . '|' . $xipv4 . '|' . $xipv6 . ')';
        $xport = '(\d*)';
        $xauthority = '((' . $xuserinfo . '@)?' . $xhost .
            '?(:' . $xport . ')?)';

        // Path from RFC3986.  Blend absolute & relative for efficiency.
        $xslash_seg = '(/[' . $xpchar . ']*)';
        $xpath_authabs = '((//' . $xauthority . ')((/[' . $xpchar . ']*)*))';
        $xpath_rel = '([' . $xpchar . ']+' . $xslash_seg . '*)';
        $xpath_abs = '(/(' . $xpath_rel . ')?)';
        $xapath = '(' . $xpath_authabs . '|' . $xpath_abs .
            '|' . $xpath_rel . ')';

        // Query and fragment from RFC3986.
        $xqueryfrag = '([' . $xpchar . '/?' . ']*)';

        // URL.
        $xurl = '^(' . $xscheme . ':)?' . $xapath . '?' .
            '(\?' . $xqueryfrag . ')?(#' . $xqueryfrag . ')?$';


        // Split the URL into components.
        if (!preg_match('!' . $xurl . '!', $url, $m))
            return false;

        $parts = array();

        if (!empty($m[2])) $parts['scheme'] = strtolower($m[2]);

        if (!empty($m[7])) {
            if (isset($m[9])) $parts['user'] = $m[9];
            else            $parts['user'] = '';
        }
        if (!empty($m[10])) $parts['pass'] = $m[11];

        if (!empty($m[13])) $h = $parts['host'] = $m[13];
        else if (!empty($m[14])) $parts['host'] = $m[14];
        else if (!empty($m[16])) $parts['host'] = $m[16];
        else if (!empty($m[5])) $parts['host'] = '';
        if (!empty($m[17])) $parts['port'] = $m[18];

        if (!empty($m[19])) $parts['path'] = $m[19];
        else if (!empty($m[21])) $parts['path'] = $m[21];
        else if (!empty($m[25])) $parts['path'] = $m[25];

        if (!empty($m[27])) $parts['query'] = $m[28];
        if (!empty($m[29])) $parts['fragment'] = $m[30];

        if (!$decode)
            return $parts;
        if (!empty($parts['user']))
            $parts['user'] = rawurldecode($parts['user']);
        if (!empty($parts['pass']))
            $parts['pass'] = rawurldecode($parts['pass']);
        if (!empty($parts['path']))
            $parts['path'] = rawurldecode($parts['path']);
        if (isset($h))
            $parts['host'] = rawurldecode($parts['host']);
        if (!empty($parts['query']))
            $parts['query'] = rawurldecode($parts['query']);
        if (!empty($parts['fragment']))
            $parts['fragment'] = rawurldecode($parts['fragment']);
        return $parts;
    }

    /**
     * Filter out "." and ".." segments from a URL's path and return
     * the result.
     *
     * This function implements the "remove_dot_segments" algorithm from
     * the RFC3986 specification for URLs.
     *
     * This function supports multi-byte characters with the UTF-8 encoding,
     * per the URL specification.
     *
     * @param $path - the path to filter
     *
     * @return string The filtered path with "." and ".." removed.
     */
    private static function urlRemoveDotSegments($path)
    {
        // multi-byte character explode
        $inSegs = preg_split('!/!u', $path);
        $outSegs = array();
        foreach ($inSegs as $seg) {
            if ($seg == '' || $seg == '.')
                continue;
            if ($seg == '..')
                array_pop($outSegs);
            else
                array_push($outSegs, $seg);
        }
        $outPath = implode('/', $outSegs);
        if ($path[0] == '/')
            $outPath = '/' . $outPath;
        // compare last multi-byte character against '/'
        if ($outPath != '/' &&
            (mb_strlen($path) - 1) == mb_strrpos($path, '/', 0, 'UTF-8'))
            $outPath .= '/';
        return $outPath;
    }

    /**
     * This function joins together URL components to form a complete URL.
     *
     * RFC3986 specifies the components of a Uniform Resource Identifier (URI).
     * This function implements the specification's "component recomposition"
     * algorithm for combining URI components into a full URI string.
     *
     * The $parts argument is an associative array containing zero or
     * more of the following:
     *
     *    "scheme"    The scheme, such as "http".
     *    "host"        The host name, IPv4, or IPv6 address.
     *    "port"        The port number.
     *    "user"        The user name.
     *    "pass"        The user password.
     *    "path"        The path, such as a file path for "http".
     *    "query"        The query.
     *    "fragment"    The fragment.
     *
     * The "port", "user", and "pass" values are only used when a "host"
     * is present.
     *
     * The optional $encode argument indicates if appropriate URL components
     * should be percent-encoded as they are assembled into the URL.  Encoding
     * is only applied to the "user", "pass", "host" (if a host name, not an
     * IP address), "path", "query", and "fragment" components.  The "scheme"
     * and "port" are never encoded.  When a "scheme" and "host" are both
     * present, the "path" is presumed to be hierarchical and encoding
     * processes each segment of the hierarchy separately (i.e., the slashes
     * are left alone).
     *
     * The assembled URL string is returned.
     *
     * @param $parts - an associative array of strings containing the
     *            individual parts of a URL.
     * @param bool $encode - an optional boolean flag selecting whether
     *            to do percent encoding or not.
     *
     * @return string Returns the assembled URL string.  The string is an absolute
     *    URL if a scheme is supplied, and a relative URL if not.  An
     *    empty string is returned if the $parts array does not contain
     *    any of the needed values.
     */
    private static function joinUrl($parts, $encode = false)
    {
        if ($encode) {
            if (isset($parts['user']))
                $parts['user'] = rawurlencode($parts['user']);
            if (isset($parts['pass']))
                $parts['pass'] = rawurlencode($parts['pass']);
            if (isset($parts['host']) &&
                !preg_match('!^(\[[\da-f.:]+\]])|([\da-f.:]+)$!ui', $parts['host']))
                $parts['host'] = rawurlencode($parts['host']);
            if (!empty($parts['path']))
                $parts['path'] = preg_replace('!%2F!ui', '/',
                    rawurlencode($parts['path']));
            if (isset($parts['query']))
                $parts['query'] = rawurlencode($parts['query']);
            if (isset($parts['fragment']))
                $parts['fragment'] = rawurlencode($parts['fragment']);
        }

        $url = '';
        if (!empty($parts['scheme']))
            $url .= $parts['scheme'] . ':';
        if (isset($parts['host'])) {
            $url .= '//';
            if (isset($parts['user'])) {
                $url .= $parts['user'];
                if (isset($parts['pass']))
                    $url .= ':' . $parts['pass'];
                $url .= '@';
            }
            if (preg_match('!^[\da-f]*:[\da-f.:]+$!ui', $parts['host']))
                $url .= '[' . $parts['host'] . ']';    // IPv6
            else
                $url .= $parts['host'];            // IPv4 or name
            if (isset($parts['port']))
                $url .= ':' . $parts['port'];
            if (!empty($parts['path']) && $parts['path'][0] != '/')
                $url .= '/';
        }
        if (!empty($parts['path']))
            $url .= $parts['path'];
        if (isset($parts['query']))
            $url .= '?' . $parts['query'];
        if (isset($parts['fragment']))
            $url .= '#' . $parts['fragment'];
        return $url;
    }
}
