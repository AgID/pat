<div class="frames-container <?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

 echo $active_frames_tab == 'application' ? 'frames-container-application' : '' ?>">
  <?php $tpl->render($frame_list) ?>
</div>