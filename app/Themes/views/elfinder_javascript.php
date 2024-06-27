<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

<script type="text/javascript">

    function _trackOperations() {
        let args = arguments;
        let countArgs = arguments.length;
        let srcType = args[0];
        let eventData = args[0];

        if (srcType == 'rename') {
            //console.log('rename');
        }

        if (srcType == 'move' || srcType == 'movie_cmd') {
            //console.log('move');
        }

        if (srcType == 'removed' || srcType == 'removed_cmd') {
            //console.log('removed');
        }

        //console.log('srcType',srcType);
    }

    function _hashDecode(hash) {
        var encPath = hash.substr(hash.indexOf('_') + 1);
        return '/' + atob(encPath.replace(/\-/g, '+').replace(/_/g, '/').replace(/\./g, '='));
    }

    $(document).ready(function () {

        let elfinder = $('#elfinder').elfinder({
            cssAutoLoad: false,
            url: '<?php echo $base_url ?>',
            lang: 'it',
            height: '100%',
            closeOnEditorCallback: true,
            resizable: true,
            // directories tree options
            tree: {
                openRootOnLoad: false,
                syncTree: true
            },
            handlers: {
                rename: function (event, elfinderInstance) {
                    _trackOperations('rename', event.data);
                },

                removed: function (event, elfinderInstance) {
                    _trackOperations('removed', event.data);
                },

                move: function (event, elfinderInstance) {
                    _trackOperations('move', event.data)
                },

                paste: function (event, elfinderInstance) {

                    let eData = event.data;

                    if (eData.hasOwnProperty('redo')) {
                        if (eData.redo.hasOwnProperty('cmd')) {
                            if (eData.redo.cmd == 'move') {
                                _trackOperations('movie_cmd', eData)
                            }
                        }
                    } else {
                        if (eData.hasOwnProperty('removed') && eData.removed.length >= 1) {
                            _trackOperations('removed_cmd', eData)
                        }
                    }

                },
            },
            commands: [
                'archive', 'colwidth', 'copy', 'cut', 'download', 'duplicate', 'extract',
                'fullscreen', 'getfile', 'info', 'mkdir', 'mkfile',
                'open', 'opendir', 'paste', 'quicklook', 'reload', 'rename', 'restore', 'rm',
                'search', 'sort', 'up', 'upload', 'zipdl', 'view'
            ]
            <?php if(!empty($isCkEditor)):?>,
            getFileCallback: function (file, fm) {
                window.opener.CKEDITOR.tools.callFunction((function () {
                    var reParam = new RegExp('(?:[\?&]|&amp;)CKEditorFuncNum=([^&]+)', 'i');
                    var match = window.location.search.match(reParam);
                    return (match && match.length > 1) ? match[1] : '';
                })(), fm.convAbsUrl(file.url));
                fm.destroy();
                window.close();
            }
            <?php endif; ?>
        });
    });
</script>


