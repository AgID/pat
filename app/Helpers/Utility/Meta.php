<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Utility;

defined('_FRAMEWORK_') or exit('No direct script access allowed');


/**
 * @method setDescription(mixed $param)
 */
class Meta
{
    private array $data = [];

    const dctermsTitle = 'title';
    const dctermsDescription = 'description';
    const dctermsPublisher = 'publisher';
    const dctermsType = 'type';
    const dctermsFormat = 'format';
    const dctermsLanguage = 'language';
    const ogTitle = 'title';
    const ogDescription = 'description';
    const ogLocale = 'locale';
    const ogType = 'type';
    const ogSiteName = 'site_name';
    const ogImage = 'image';
    const ogImageUrl = 'image:url';
    const ogImageSecureUrl = 'image:secure_url';
    const ogImageWidth = 'image:width';
    const ogImageHeight = 'image:height';
    const ogImageType = 'image:type';
    const twitterCard = 'card';
    const twitterTitle = 'title';
    const twitterDescription = 'description';
    const twitterCreator = 'creator';
    const twitterImage = 'image';

    /**
     * @return static
     */
    public static function getInstance(): static
    {
        return new static();
    }

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @param string|null $title Valore da settare nel tag title
     * @return $this
     */
    public function tagTitle(?string $title = null): static
    {

        if ($title !== null) {
            $this->data[] = '<title>' . $title . '</title>';
        }

        return $this;

    }

    /**
     * @param string     $method    Metodo da chiamare
     * @param array|null $arguments Argomenti da passare al metodo
     * @return static
     */
    public static function __callStatic(string $method, ?array $arguments)
    {
        $vars = count($arguments) ? current($arguments) : [];
        return new static($method, $vars);
    }

    /**
     * @param string     $method    Metodo da chiamare
     * @param array|null $arguments Argomenti da passare al metodo
     * @return $this
     */
    public function __call(string $method, ?array $arguments)
    {
        $data = [];
        $found = false;
        if (strlen($method) === 3 && $method === 'set' && is_array($arguments)) {

            $this->setAttributes($arguments);

        } else {

            $method = strtolower(substr($method, 3, strlen($method)));

            if (substr($method, 0, 7) === 'dcterms') {

                $data['name'] = $method . ':' . $arguments[0];

                if (!empty($arguments[1]) && !is_array($arguments[1])) {
                    $found = true;
                    $data['content'] = $arguments[1];
                } else if (!empty($arguments[1])) {
                    $found = true;
                    $data = array_merge($data['name'], $arguments[1]);
                }

            } elseif (substr($method, 0, 2) === 'og') {

                $data['name'] = $method . ':' . $arguments[0];

                if (!empty($arguments[1]) && !is_array($arguments[1])) {
                    $found = true;
                    $data['content'] = $arguments[1];
                } else {
                    $found = true;
                    $data = array_merge($data['name'], $arguments[1]);
                }

            } elseif (substr($method, 0, 7) === 'twitter') {

                $data['name'] = $method . ':' . $arguments[0];

                if (!empty($arguments[1]) && !is_array($arguments[1])) {
                    $found = true;
                    $data['content'] = $arguments[1];
                } else {
                    $found = true;
                    $data = array_merge($data['name'], $arguments[1]);
                }


            } elseif (substr($method, 0, 12) === 'lastmodified') {

                $data['name'] = substr($method, 0, 4) . '-' . substr($method, 4, 8);

                if (!empty($arguments[0])) {
                    if (!is_array($arguments[0])) {
                        $found = true;
                        $data['content'] = $arguments[0];
                    } else {
                        $found = true;
                        $data = array_merge($data['name'], $arguments[0]);
                    }
                }

            } else {

                $data['name'] = $method;

                if (!empty($arguments[0])) {
                    if (!is_array($arguments[0])) {
                        $found = true;
                        $data['content'] = $arguments[0];
                    } else {
                        $found = true;
                        $data = array_merge($data['name'], $arguments[0]);
                    }
                }
            }


            if ($found) {
                $this->data[] = $this->setAttributes($data);
            }
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function toHtml(): ?string
    {

        if (count($this->data) >= 1) {
            return implode("\n", array_reverse($this->data));
        }

        return null;
    }

    /**
     * @param array|string $attributes Attributo da settare
     * @return string|null
     */
    private function setAttributes(array|string $attributes = ''): ?string
    {
        $atts = null;

        if (empty($attributes)) {

            return null;
        }

        if (is_string($attributes)) {

            return ' ' . $attributes;
        }

        $attributes = (array)$attributes;

        foreach ($attributes as $key => $val) {

            $atts .= $key . '="' . strip_tags((string)escapeXss($val)) . '" ';
        }

        return '<meta ' . trim($atts) . ' />';
    }
}
