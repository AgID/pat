<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class Parser
{

    public $lDelim = '{';
    public $rDelim = '}';
    protected $CI;

    public function render($template, $data)
    {
        $template = $this->parseConditionals($template, $data);
        return $this->parse($template, $data);
    }

    protected function parse($template, $data)
    {
        if ($template === '') {
            return false;
        }

        $replace = [];
        foreach ($data as $key => $val) {
            $replace = array_merge(
                $replace,
                is_array($val)
                    ? $this->parsePair($key, $val, $template)
                    : $this->parseSingle($key, (string) $val, $template)
            );
        }

        unset($data);
        $template = strtr($template, $replace);

        return $template;
    }

    public function setDelimiters($l = '{', $r = '}')
    {
        $this->lDelim = $l;
        $this->rDelim = $r;
    }

    protected function parseSingle($key, $val, $string)
    {
        return [$this->lDelim . $key . $this->rDelim => (string) $val];
    }

    protected function parsePair($variable, $data, $string)
    {
        $replace = [];
        preg_match_all(
            '#' . preg_quote($this->lDelim . $variable . $this->rDelim) . '(.+?)' . preg_quote($this->lDelim . '/' . $variable . $this->rDelim) . '#s',
            $string,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $match) {
            $str = '';
            foreach ($data as $row) {
                $temp = [];
                foreach ($row as $key => $val) {
                    if (is_array($val)) {
                        $pair = $this->parsePair($key, $val, $match[1]);
                        if (!empty($pair)) {
                            $temp = array_merge($temp, $pair);
                        }

                        continue;
                    }

                    $temp[$this->lDelim . $key . $this->rDelim] = $val;
                }

                $str .= strtr($match[1], $temp);
            }

            $replace[$match[0]] = $str;
        }

        return $replace;
    }

    protected function parseConditionals($template, $data)
    {

        $pattern = '/{if\s*(.+?)}(.*?){(?:else)}(.*?){\/if}/s';

        return preg_replace_callback($pattern, function ($matches) use ($data) {

            $conditionResult = $this->evaluateExpression($matches[1], $data);

            return $conditionResult ? $matches[2] : $matches[3];
        }, $template);
    }

    protected function evaluateExpression($expression, $data)
    {
        $tokens = preg_split("/(\s+|\|\||&&|[()])/", $expression, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        return $this->evaluateRPN($tokens, $data);
    }

    protected function evaluateRPN($queue, $data)
    {
        $stack = [];

        foreach ($queue as $token) {
            if (in_array($token, ['&&', '||'])) {
                $right = array_pop($stack);
                $left = array_pop($stack);
                switch ($token) {
                    case '&&':
                        $stack[] = $left && $right;
                        break;
                    case '||':
                        $stack[] = $left || $right;
                        break;
                }
            } elseif (preg_match("/^(!?)([\w.]+)(==|!=|>=|<=|>|<)\s*'?([^']*)'?$/", $token, $matches)) {
                $negate = $matches[1];
                $variable = $matches[2];
                $operator = $matches[3];
                $value = $matches[4];

                $resolvedValue = isset($data[$variable]) ? $data[$variable] : null;
                $result = true;

                if ($operator) {
                    switch ($operator) {
                        case '==':
                            $result = $resolvedValue == $value;
                            break;
                        case '!=':
                            $result = $resolvedValue != $value;
                            break;
                        case '>=':
                            $result = $resolvedValue >= $value;
                            break;
                        case '<=':
                            $result = $resolvedValue <= $value;
                            break;
                        case '>':
                            $result = $resolvedValue > $value;
                            break;
                        case '<':
                            $result = $resolvedValue < $value;
                            break;
                    }
                } else {
                    $result = (bool)$resolvedValue;
                }

                if ($negate) {
                    $result = !$result;
                }

                $stack[] = $result;
            }
        }

        return array_pop($stack);
    }
}
