<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

<div class="col-md-12">
    {{--  Facebook --}}
    <div class="form-group">
        <label for="facebook">Facebook</label>
        <div class="input-group">
            <div class="input-group-prepend">
            <span class="input-group-text">
                <i class="fab fa-facebook-f"></i>
            </span>
            </div>
            {{ form_input([
                'name' => 'facebook',
                'value' => !empty($socials['facebook']) ? $socials['facebook'] : null,
                'placeholder' => 'https://www.',
                'id' => 'input_facebook',
                'class' => 'form-control input_facebook'
            ]) }}
        </div>
    </div>
</div>

<div class="col-md-12">
    {{--  Twitter --}}
    <div class="form-group">
        <label for="twitter">Twitter</label>
        <div class="input-group">
            <div class="input-group-prepend">
            <span class="input-group-text">
                <i class="fab fa-twitter"></i>
            </span>
            </div>
            {{ form_input([
                'name' => 'twitter',
                'value' => !empty($socials['twitter']) ? $socials['twitter'] : null,
                'placeholder' => 'https://www.',
                'id' => 'input_twitter',
                'class' => 'form-control input_twitter'
            ]) }}
        </div>
    </div>
</div>

<div class="col-md-12">
    {{--  youtube --}}
    <div class="form-group">
        <label for="linkedin">Youtube</label>
        <div class="input-group">
            <div class="input-group-prepend">
            <span class="input-group-text">
                <i class="fab fa-youtube"></i>
            </span>
            </div>
            {{ form_input([
                'name' => 'youtube',
                'value' => !empty($socials['youtube']) ? $socials['youtube'] : null,
                'placeholder' => 'https://www.',
                'id' => 'input_youtube',
                'class' => 'form-control input_youtube'
            ]) }}
        </div>
    </div>
</div>

<div class="col-md-12">
    {{--  Instagram --}}
    <div class="form-group">
        <label for="instagram">Instagram</label>
        <div class="input-group">
            <div class="input-group-prepend">
            <span class="input-group-text">
               <i class="fab fa-instagram"></i>
            </span>
            </div>
            {{ form_input([
                'name' => 'instagram',
                'value' => !empty($socials['instagram']) ? $socials['instagram'] : null,
                'placeholder' => 'https://www.',
                'id' => 'input_instagram',
                'class' => 'form-control input_instagram'
            ]) }}
        </div>
    </div>
</div>

<div class="col-md-12">
    {{--  LinkedIn --}}
    <div class="form-group">
        <label for="linkedin">LinkedIn</label>
        <div class="input-group">
            <div class="input-group-prepend">
            <span class="input-group-text">
                 <i class="fab fa-linkedin-in"></i>
            </span>
            </div>
            {{ form_input([
                'name' => 'linkedin',
                'value' => !empty($socials['linkedin']) ? $socials['linkedin'] : null,
                'placeholder' => 'https://www.',
                'id' => 'input_linkedin',
                'class' => 'form-control input_linkedin'
            ]) }}
        </div>
    </div>
</div>

<div class="col-md-12">
    {{--  WhatsApp --}}
    <div class="form-group">
        <label for="whatsapp">WhatsApp</label>
        <div class="input-group">
            <div class="input-group-prepend">
            <span class="input-group-text">
                 <i class="fab fa-whatsapp"></i>
            </span>
            </div>
            {{ form_input([
                'name' => 'whatsApp',
                'value' => !empty($socials['whatsapp']) ? $socials['whatsapp'] : null,
                'placeholder' => 'https://www.',
                'id' => 'input_whatsapp',
                'class' => 'form-control input_whatsapp'
            ]) }}
        </div>
    </div>
</div>

<div class="col-md-12">
    {{--  TikTok --}}
    <div class="form-group">
        <label for="tiktok">TikTok</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">
                       <i class="fab fa-tiktok"></i>
                </span>
            </div>
            {{ form_input([
                'name' => 'tiktok',
                'value' => !empty($socials['tiktok']) ? $socials['tiktok'] : null,
                'placeholder' => 'https://www.',
                'id' => 'input_tiktok',
                'class' => 'form-control input_tiktok'
            ]) }}
        </div>
    </div>
</div>

<div class="col-md-12">
    {{--  WeChat --}}
    <div class="form-group">
        <label for="wechat">WeChat</label>
        <div class="input-group">
            <div class="input-group-prepend">
            <span class="input-group-text">
                 <i class="fab fa-weixin"></i>
            </span>
            </div>
            {{ form_input([
                'name' => 'wechat',
                'value' => !empty($socials['wechat']) ? $socials['wechat'] : null,
                'placeholder' => 'https://www.',
                'id' => 'input_wechat',
                'class' => 'form-control input_wechat'
            ]) }}
        </div>
    </div>
</div>

<div class="col-md-12">
    {{--  Pinterest --}}
    <div class="form-group">
        <label for="pinterest">Pinterest</label>
        <div class="input-group">
            <div class="input-group-prepend">
            <span class="input-group-text">
                <i class="fab fa-pinterest"></i>
            </span>
            </div>
            {{ form_input([
                'name' => 'pinterest',
                'value' => !empty($socials['pinterest']) ? $socials['pinterest'] : null,
                'placeholder' => 'https://www.',
                'id' => 'input_pinterest',
                'class' => 'form-control input_pinterest'
            ]) }}
        </div>
    </div>
</div>

<div class="col-md-12">
    {{--  Twitch --}}
    <div class="form-group">
        <label for="twitch">Twitch</label>
        <div class="input-group">
            <div class="input-group-prepend">
            <span class="input-group-text">
                 <i class="fab fa-twitch"></i>
            </span>
            </div>
            {{ form_input([
                'name' => 'twitch',
                'value' => !empty($socials['twitch']) ? $socials['twitch'] : null,
                'placeholder' => 'https://www.',
                'id' => 'input_twitch',
                'class' => 'form-control input_twitch'
            ]) }}
        </div>
    </div>
</div>

<div class="col-md-12">
    {{--  Reddit --}}
    <div class="form-group">
        <label for="reddit">Reddit</label>
        <div class="input-group">
            <div class="input-group-prepend">
            <span class="input-group-text">
                 <i class="fab fa-reddit"></i>
            </span>
            </div>
            {{ form_input([
                'name' => 'reddit',
                'value' => !empty($socials['reddit']) ? $socials['reddit'] : null,
                'placeholder' => 'https://www.',
                'id' => 'input_reddit',
                'class' => 'form-control input_reddit'
            ]) }}
        </div>
    </div>
</div>

<div class="col-md-12">
    {{--  Tumblr --}}
    <div class="form-group">
        <label for="tumblr">Tumblr</label>
        <div class="input-group">
            <div class="input-group-prepend">
            <span class="input-group-text">
                 <i class="fab fa-tumblr"></i>
            </span>
            </div>
            {{ form_input([
                'name' => 'tumblr',
                'value' => !empty($socials['tumblr']) ? $socials['tumblr'] : null,
                'placeholder' => 'https://www.',
                'id' => 'input_tumblr',
                'class' => 'form-control input_tumblr'
            ]) }}
        </div>
    </div>
</div>

<div class="col-md-12">
    {{--  Snapchat --}}
    <div class="form-group">
        <label for="snapchat">Snapchat</label>
        <div class="input-group">
            <div class="input-group-prepend">
            <span class="input-group-text">
                    <i class="fab fa-snapchat"></i>
            </span>
            </div>
            {{ form_input([
                'name' => 'snapchat',
                'value' => !empty($socials['snapchat']) ? $socials['snapchat'] : null,
                'placeholder' => 'https://www.',
                'id' => 'input_snapchat',
                'class' => 'form-control input_snapchat'
            ]) }}
        </div>
    </div>
</div>
