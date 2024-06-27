<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');


/**
 * stream - Handle raw input stream
 *
 * LICENSE: This source file is subject to version 3.01 of the GPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/gpl.html. If you did not receive a copy of
 * the GPL License and are unable to obtain it through the web, please
 *
 * @author jason.gerfen@gmail.com
 * @license http://www.gnu.org/licenses/gpl.html GPL License 3
 */
class Stream
{
    /**
     * @abstract Raw input stream
     */
    protected $input;

    protected $resulFiles;

    /**
     * @function __construct
     *
     * @param array $data stream
     */
    public function __construct(array &$data)
    {
        $this->input = file_get_contents('php://input');

        $boundary = $this->boundary();

        if (!strlen((string)$boundary)) {
            $data = [
                'post' => $this->parse(),
                'file' => []
            ];
        } else {
            $blocks = $this->split($boundary);

            $data = $this->blocks($blocks);
        }

        return $data;
    }

    /**
     * @function boundary
     * @returns string
     */
    private function boundary()
    {
        if (!isset($_SERVER['CONTENT_TYPE'])) {
            return null;
        }

        preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);
        return @$matches[1];
    }

    /**
     * @function parse
     * @returns array
     */
    private function parse()
    {
        parse_str(urldecode($this->input), $result);
        return $result;
    }

    /**
     * @function split
     * @param $boundary string
     * @returns array
     */
    private function split($boundary)
    {
        $result = preg_split("/-+$boundary/", $this->input);
        array_pop($result);
        return $result;
    }

    /**
     * @function blocks
     * @param $array array
     * @returns array
     */
    private function blocks($array)
    {
        $this->resulFiles = [
            'post' => [],
            'file' => []
        ];

        foreach ($array as $key => $value) {
            if (empty($value))
                continue;

            $block = $this->decide($value);

            if (count($block['post']) > 0) {
                array_push($this->resulFiles['post'], $block['post']);
            }

            if (count($block['file']) > 0) {
                $k = array_key_first($block['file']);
                $tmp = $block['file'][$k];
                $this->resulFiles['file'][$k]['name'][] = $tmp['name'];
                $this->resulFiles['file'][$k]['type'][] = $tmp['type'];
                $this->resulFiles['file'][$k]['tmp_name'][] = $tmp['tmp_name'];
                $this->resulFiles['file'][$k]['error'][] = $tmp['error'];
                $this->resulFiles['file'][$k]['size'][] = $tmp['size'];
            }

        }

        return $this->merge($this->resulFiles);
    }

    /**
     * @function decide
     * @param $string string
     * @returns array
     */
    private function decide($string)
    {
        if (strpos($string, 'application/octet-stream') !== FALSE) {
            return [
                'post' => $this->file($string),
                'file' => []
            ];
        }

        if (strpos($string, 'filename') !== FALSE) {
            return [
                'post' => [],
                'file' => $this->file_stream($string)
            ];
        }

        return [
            'post' => $this->post($string),
            'file' => []
        ];
    }

    /**
     * @function file
     *
     * @param $string
     *
     * @return array
     */
    private function file($string)
    {
        preg_match('/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s', $string, $match);
        return [
            $match[1] => (!empty($match[2]) ? $match[2] : '')
        ];
    }

    /**
     * @function file_stream
     *
     * @param $string
     *
     * @return array
     */
    private function file_stream($string)
    {
        $data = [];

        preg_match('/name=\"([^\"]*)\"; filename=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $string, $match);
        preg_match('/Content-Type: (.*)?/', $match[3], $mime);

        $image = preg_replace('/Content-Type: (.*)[^\n\r]/', '', $match[3]);

        $path = sys_get_temp_dir() . '/php' . substr(sha1(rand()), 0, 6);

        $err = file_put_contents($path, ltrim($image));

        if (preg_match('/^(.*)\[\]$/i', $match[1], $tmp)) {
            $index = $tmp[1];
        } else {
            $index = $match[1];
        }

        $data[$index]['name'][] = $match[2];
        $data[$index]['type'][] = $mime[1];
        $data[$index]['tmp_name'][] = $path;
        $data[$index]['error'][] = ($err === FALSE) ? $err : 0;
        $data[$index]['size'][] = filesize($path);

        return $data;
    }

    /**
     * @function post
     *
     * @param $string
     *
     * @return array
     */
    private function post($string)
    {
        $data = [];

        preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $string, $match);

        if (preg_match('/^(.*)\[\]$/i', $match[1], $tmp)) {
            //$data[$tmp[1]][] = (!empty($match[2]) ? $match[2] : '');
            $data[$tmp[1]][] = @$match[2];
        } else {
            // $data[$match[1]] = (!empty($match[2]) ? $match[2] : '');
            $data[$match[1]] = @$match[2];
        }

        return $data;
    }

    /**
     * @function merge
     * @param $array array
     *
     * Ugly ugly ugly
     *
     * @returns array
     */
    private function merge($array)
    {
        $results = [
            'post' => [],
            'file' => []
        ];

        if (count($array['post']) > 0) {
            foreach ($array['post'] as $key => $value) {
                foreach ($value as $k => $v) {
                    if (is_array($v)) {
                        foreach ($v as $kk => $vv) {
                            $results['post'][$k][] = $vv;
                        }
                    } else {
                        $results['post'][$k] = $v;
                    }
                }
            }
        }

        if (count($array['file']) > 0) {
            foreach ($array['file'] as $key => $value) {
                foreach ($value as $k => $v) {
                    if (is_array($v)) {
                        foreach ($v as $kk => $vv) {
                            if (is_array($vv) && (count($vv) === 1)) {
                                $results['file'][$k][$kk] = $vv[0];
                            } else {
                                $results['file'][$k][$kk][] = $vv[0];
                            }
                        }
                    } else {
                        $results['file'][$k][$key] = $v;
                    }
                }
            }
        }

        return $results;
    }
}