# CKEditor WordCount Plugin — componente di terze parti

Plugin **non** compreso nel build di CKEditor: non compare nella lista `plugins` di
`assets/common/ckeditor4/build-config.js` e non è compilato dentro `ckeditor.js`.
È stato aggiunto a mano nella cartella dei plugin.

- Nome: CKEditor WordCount Plugin
- Versione: 1.17.10 (vedi `version` in `plugin.js`)
- Origine: https://github.com/w8tcha/CKEditor-WordCount-Plugin
- Licenza: MIT, vedi `LICENSE.md` in questa cartella
- Titolare del copyright: Ingo Herbote

Nota sull'intestazione di `plugin.js`: il file riporta un header generico
`Copyright (c) CKSource - Frederico Knabben. All rights reserved.` con rimando a
`http://ckeditor.com/license`, che è un URL non più valido. Non è la licenza
effettiva del plugin: quella è la MIT riportata in `LICENSE.md`. L'header non è
stato modificato per non alterare un file di terze parti.

Dipendenze dichiarate in `plugin.js`: `htmlwriter`, `notification`, `undo`. Tutte
e tre sono compilate dentro `ckeditor.js`, quindi non servono cartelle aggiuntive.

Il plugin viene attivato a runtime, aggiungendolo a `CKEDITOR.config.extraPlugins`, da
`app/Themes/admin/layout/partials/footer.blade.php`.
