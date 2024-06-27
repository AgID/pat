<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class Breadcrumbs
{

    private $breadcrumbs = [];
    private $tagOpen;
    private $tagClose;
    private $divider;
    private $crumbOpen;
    private $crumbClose;
    private $crumbLastOpen;
    private $crumbDivider;
    private $baseUrl;
    private $icoHome;

    public function __construct($options = null, $label = null)
    {

        $label = $label !== null ? $label . '.' : '';

        $this->tagOpen = (($options !== null) && !empty($options['tag_open']))
            ? $options['tag_open'] : config($label . 'tag_open', null, 'breadcrumbs');

        $this->tagClose = (($options !== null) && !empty($options['tag_close']))
            ? $options['tag_close'] : config($label . 'tag_close', null, 'breadcrumbs');

        $this->divider = (($options !== null) && !empty($options['divider']))
            ? $options['divider'] : config($label . 'divider', null, 'breadcrumbs');

        $this->crumbOpen = (($options !== null) && !empty($options['crumb_open']))
            ? $options['crumb_open'] : config($label . 'crumb_open', null, 'breadcrumbs');

        $this->crumbClose = (($options !== null) && !empty($options['crumb_close']))
            ? $options['crumb_close'] : config($label . 'crumb_close', null, 'breadcrumbs');

        $this->crumbLastOpen = (($options !== null) && !empty($options['crumb_last_open']))
            ? $options['crumb_last_open'] : config($label . 'crumb_last_open', null, 'breadcrumbs');

        $this->crumbDivider = (($options !== null) && !empty($options['crumb_divider']))
            ? $options['crumb_divider'] : config($label . 'crumb_divider', null, 'breadcrumbs');

        $this->baseUrl = (($options !== null) && !empty($options['base_url']))
            ? $options['base_url'] : config($label . 'base_url', null, 'breadcrumbs');

        $this->icoHome = (($options !== null) && !empty($options['ico_home']))
            ? $options['ico_home'] : config($label . 'ico_home', null, 'breadcrumbs');


    }

    function push($page, $href)
    {
        if (!$page or !$href) return;

        $href = baseUrl($href);

        $this->breadcrumbs[$href] = ['page' => $page, 'href' => $href];
    }

    function unshift($page, $href)
    {
        if (!$page or !$href) return;

        $href = site_url($href);

        array_unshift($this->breadcrumbs, ['page' => $page, 'href' => $href]);
    }

    function show()
    {
        $output = $this->tagOpen;
        $output .= $this->crumbOpen . '<a href="' . baseUrl($this->baseUrl) . '">';
        $output .= $this->icoHome . ' Home</a> ';
        $output .= $this->crumbDivider;
        $output .= $this->crumbClose;

        if ($this->breadcrumbs) {

            foreach ($this->breadcrumbs as $key => $crumb) {

                $keys = array_keys($this->breadcrumbs);

                if (end($keys) == $key) {

                    $output .= $this->crumbLastOpen . '' . escapeXss($crumb['page']) . '' . $this->crumbClose;

                } else {

                    $output .= $this->crumbOpen . '<a href="' . escapeXss($crumb['href'],true,false) . '">' . escapeXss($crumb['page']) . '</a> ' . $this->crumbDivider . $this->crumbClose;

                }
            }

        }

        return $output . $this->tagClose . PHP_EOL;
    }

}