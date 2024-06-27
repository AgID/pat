# Classe per la creazione dei campi di un modulo (Form)

**Riferimento path sorgente classe upload:** *core/Helpers/Form.php*

Il Framework offre una libreria per la creazione dei vari campi di un modulo.



**Esempio pratico della creazione dei campi  di un modulo**:

Nella vista creare un modulo contenente i campi di input desiderati,  come l'esempio di seguito:

```php+HTML
{{ form_open_multipart($formAction,$formSettings) }}
   <span>Aggiunta Utente</span>
   {{-- BEGIN: Form --}}
   {{-- Campo Nome utente --}}
   <div class="form-group col-md-6">
     <label for="name">Nome utente *</label>
     {{ form_input([
         'name' => 'name',
         'value' => !empty($user['name']) ? $user['name'] : null,
         'placeholder' => 'Nome utente',
         'id' => 'input_name',
         'class' => 'form-control input_name',
     ]) }}
  </div>

  {{-- Campo Username --}}
  <div class="form-group col-md-6">
    <label for="username">Username *</label>
    {{ form_input([
	    'name' => 'username',
    	'value' => !empty($user['username']) ? $user['username'] : null,
    	'placeholder' => 'Username',
    	'id' => 'input_username',
    	'class' => 'form-control input_username',
    ]) }}
  </div>

  {{-- Campo Password --}}
  <div class="form-group col-md-6">
  	<label for="password">Password *</label>
    {{ form_password([
    	'name' => 'password',
    	'value' => '',
    	'placeholder' => 'Password',
    	'id' => 'input_password',
    	'class' => 'form-control input_password',
    ]) }}
  </div>

  {{-- Campo Indirizzo email --}}
  <div class="form-group col-md-6">
    <label for="email">Email *</label>
    {{ form_input([
   		'name' => 'email',
   		'value' => !empty($user['email']) ? $user['email'] : null,
   		'placeholder' => 'Email',
   		'id' => 'input_email',
   		'class' => 'form-control input_email',
    ]) }}
  </div>

  <div class="select2-blue" id="input_registration_type">
    {{ form_dropdown(
      		'registration_type',
            [
             '' => '',
             0 => 'Registra utente attivo senza mail di notifica',
             1 => 'Registra utente attivo con mail di notifica',
             2 => 'Registra utente come bloccato ed invia email per l\'attivazione'
            ],
            @$user['registration_type'],
            'class="form-control select2-registration_type" style="width: 100%;"'
    ) }}
  </div>

{{ form_close() }}
```



#### **Lista dei metodi per la creazione dei campi delle forms:**

- `form_open($action = '', $attributes = [], $hidden = [], $csfr_token = false)`
- `form_open_multipart($action = '', $attributes = [], $hidden = [], $csfr_token = false)`
- `form_hidden($name = '', $value = '', $recursing = FALSE)`
- `form_input($data = '', $value = '', $extra = '')`
- `form_password($data = '', $value = '', $extra = '')`
- `form_upload($data = '', $extra = '')`
- `form_textarea($data = '', $value = '', $extra = '')`
- `form_dropdown($data = '', $options = [], $selected = [], $extra = '')`
- `form_multiselect($name = '', $options = [], $selected = [], $extra = '')`
- `form_checkbox($data = '', $value = '', $checked = FALSE, $extra = '')`
- `form_radio($data = '', $value = '', $checked = FALSE, $extra = '')`
- `form_submit($data = '', $value = '', $extra = '')`
- `form_reset($data = '', $value = '', $extra = '')`
- `form_button($data = '', $content = '', $extra = '')`
- `form_label($label_text = '', $id = '', $attributes = [])`
- `form_fieldset($legend_text = '', $attributes = [])`
- `form_fieldset_close($extra = '')`
- `form_close($extra = '')`
- `_parse_form_attributes($attributes, $default)`



#### Riferimenti funzioni.

`form_open($action = '', $attributes = [], $hidden = [], $csfr_token = false)`



Funzione che crea un tag di apertura del form (<form>) con un URL di base costruito dalle preferenze di configurazione.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$action** (*string*) - Azione/target URI che viene eseguita al submit del modulo del form<br />**$attributes** (*array*) - Attributi HTML <br />**$hidden** (*array*) - Un array dei campi da nascondere nel modulo<br />**$csfr_token** (bool) - Genera un campo nascosto per Cross-Site-Forgery-Request |
| **Ritorno**         | Un tag HTML di apertura del form                             |
| **Tipo di ritorno** | string                                                       |



Esempio:

```php+HTML
{{ form_open(
		'/admin/user/store',
		[   'name' => 'form_user',
            'id' => 'form_user',
            'class' => 'form_user'
        ]
) }}
```

L'esempio precedente creerebbe un modulo che punta al tuo URL di base più i segmenti URI "/admin/user/store", come questo:

```html
<form action="http://patos.local/admin/user/store.html" name="form_user" id="form_user" class="form_user" 			          method="post" accept-charset="utf-8">
```



------

`form_open_multipart($action = '', $attributes = [], $hidden = [], $csfr_token = false)`



Questa funzione è assolutamente identica a `form_open()` di cui sopra, tranne che aggiunge un attributo multipart, che è necessario se si desidera utilizzare il modulo per caricare file.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$action** (*string*) - Azione/target URI che viene eseguita al submit del form<br />**$attributes** (*array*) - Attributi HTML <br />**$hidden** (*array*) - Un array dei campi da nascondere nel form<br />**$csfr_token** (bool) - Genera un campo nascosto per Cross-Site-Forgery-Request |
| **Ritorno**         | Un tag HTML di apertura form multipart                       |
| **Tipo di ritorno** | string                                                       |



Esempio:

```php+HTML
{{ form_open_multipart(
		'/admin/user/store',
		[   'name' => 'form_user',
            'id' => 'form_user',
            'class' => 'form_user'
        ]
) }}
```

L'esempio precedente creerebbe un modulo che punta al tuo URL di base più i segmenti URI "/admin/user/store", come questo:

```html
<form action="http://patos.local/admin/user/store.html" name="form_user" id="form_user" class="form_user" enctype="multipart/form-data" method="post" accept-charset="utf-8">
```



------

`form_hidden($name = '', $value = '', $recursing = FALSE)`



Questa funzione permette di generare campi di input nascosti.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$name** (*string*) - Nome del campo di input da nascondere <br />**$value** (*string*) - Valore del campo di input da nascondere |
| **Ritorno**         | Un tag HTML di input nascosto                                |
| **Tipo di ritorno** | string                                                       |



Esempio:

```php+HTML
{{ form_hidden('username','testUsername') }}
```

L'esempio precedente creerebbe un campo di input nascosto come il seguente:

```html
<input type="hidden" name="username" value="testUsername" />
```



------

`form_input($data = '', $value = '', $extra = '')`



Funzione che permette di generare un campo di input di testo standard.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$data** (*array*) - Dati degli attributi del campo <br />**$value** (*string*) - Valore del campo di input<br />**$extra** (*mixed*) - Attributi extra da aggiungere al tag come un array o una stringa letterale |
| **Ritorno**         | Un tag HTML di input di testo                                |
| **Tipo di ritorno** | string                                                       |



Esempio:

```php+HTML
{{ form_input([
	'name' => 'name',
    'value' => 'Joe',
    'placeholder' => 'Nome utente',
    'id' => 'input_name',
    'class' => 'form-control input_name',
]) }}
```

L'esempio precedente creerebbe un campo di input di testo come il seguente:

```html
<input type="text" name="name" value="Joe" placeholder="Nome utente" id="input_name" class="form-control input_name"  />
```



------

`form_password($data = '', $value = '', $extra = '')`



Questa funzione è identica in tutto e per tutto alla funzione `form_input()` di cui sopra, tranne che usa il tipo di input "password".

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$data** (*array*) - Dati degli attributi del campo <br />**$value** (*string*) - Valore del campo di input<br />**$extra** (*mixed*) - Attributi extra da aggiungere al tag come un array o una stringa letterale |
| **Ritorno**         | Un tag HTML di input password                                |
| **Tipo di ritorno** | string                                                       |



Esempio:

```php+HTML
{{ form_password([
	'name' => 'password',
    'value' => '',
    'placeholder' => 'Password',
    'id' => 'input_password',
    'class' => 'form-control input_password',
]) }}
```

L'esempio precedente creerebbe un campo di input di testo come il seguente:

```html
<input type="password" name="password" value="" placeholder="Password" id="input_password" class="form-control input_password"  />
```



------

`form_upload($data = '', $extra = '')`



Questa funzione è identica in tutto e per tutto alla funzione `form_input()` di cui sopra, eccetto che usa il tipo di input "file", permettendole di essere usata per caricare file.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$data** (*array*) - Dati degli attributi del campo <br />**$value** (*string*) - Valore del campo di input<br />**$extra** (*mixed*) - Attributi extra da aggiungere al tag come un array o una stringa letterale |
| **Ritorno**         | Un tag HTML di input per l'upload di un file                 |
| **Tipo di ritorno** | string                                                       |



Esempio:

```
{{ form_upload([
	'name' => 'userfile',
    'class' => 'input_userfile',
    'id' => 'input_userfile',
]) }}
```

L'esempio precedente creerebbe un campo di input di testo come il seguente:

```html
 <input type="file" name="userfile" class="input_userfile" id="input_userfile" />
```



------

`form_textarea($data = '', $value = '', $extra = '')`



Questa funzione è identica in tutto e per tutto alla funzione `form_input()` di cui sopra, tranne che genera un tipo "textarea".

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$data** (*array*) - Dati degli attributi del campo <br />**$value** (*string*) - Valore del campo di input<br />**$extra** (*mixed*) - Attributi extra da aggiungere al tag come un array o una stringa letterale |
| **Ritorno**         | Un tag HTML di input di tipo textarea                        |
| **Tipo di ritorno** | string                                                       |



Esempio:

```php+HTML
{{ form_textarea([
	'name' => 'notes',
    'value' => 'Note utente',
    'placeholder' => 'Note',
    'id' => 'input_notes',
    'class' => 'form-control input_notes',
    'cols' => '10',
    'rows' => '3',
]) }}
```

L'esempio precedente creerebbe un campo di input di tipo textarea come il seguente:

```html
<textarea name="notes" cols="10" rows="3" value="Note utente" placeholder="Note" id="input_notes" class="form-control input_notes" ></textarea>
```



------

`form_dropdown($data = '', $options = [], $selected = [], $extra = '')`



Funzione che permette di creare un campo di input di tipo select standard. Il primo parametro conterrà il nome del campo, il secondo parametro conterrà un array associativo di opzioni, e il terzo parametro conterrà il valore che desideri sia selezionato.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$name** (*array*) - Nome del campo di input di tipo select <br />**$options** (*array*) - Un array associativo di opzioni da elencare tra cui scegliere<br />**$selected** (array) - Elenco di campi da marcare con l'attributo selezionato<br />**$extra** (*mixed*) - Attributi extra da aggiungere al tag come un array o una stringa letterale |
| **Ritorno**         | Un tag HTML di input di tipo select standard                 |
| **Tipo di ritorno** | string                                                       |



Esempio:

```
{{ form_dropdown(
	'registration_type',
    [
    	'' => '',
        0 => 'Registra utente attivo senza mail di notifica',
        1 => 'Registra utente attivo con mail di notifica',
        2 => 'Registra utente come bloccato ed invia email per l\'attivazione'
    ],
    2,
    'class="form-control select2-registration_type" style="width: 100%;"'
) }}
```

L'esempio precedente creerebbe un campo di input di tipo select come il seguente:

```html
<select name="registration_type" class="form-control select2-registration_type" style="width: 100%;">
<option value=""></option>
<option value="0">Registra utente attivo senza mail di notifica</option>
<option value="1">Registra utente attivo con mail di notifica</option>
<option value="2" selected="selected">Registra utente come bloccato ed invia email per l'attivazione</option>
</select>
```



------

`form_multiselect($name = '', $options = [], $selected = [], $extra = '')`



Funzione che permette di creare un campo multiselect standard. Il primo parametro conterrà il nome del campo, il secondo parametro conterrà un array associativo di opzioni e il terzo parametro conterrà il valore o i valori da selezionare.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$name** (*array*) - Nome del campo di input di tipo select <br />**$options** (*array*) - Un array associativo di opzioni da elencare tra cui scegliere<br />**$selected** (array) - Elenco di campi da marcare con l'attributo selezionato<br />**$extra** (*mixed*) - Attributi extra da aggiungere al tag come un array o una stringa letterale |
| **Ritorno**         | Un tag HTML di input di tipo multiselect standard            |
| **Tipo di ritorno** | string                                                       |

L'uso dei parametri è identico all'uso di `form_dropdown()` sopra, eccetto ovviamente che il nome del campo dovrà usare la sintassi dell'array POST.



------

`form_checkbox($data = '', $value = '', $checked = FALSE, $extra = '')`



Funzione che permette di generare un campo di input di tipo checkbox.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$data** (*array*) - Dati degli attributi del campo <br />**$value** (*string*) - Valore del campo di input<br />**$checked** (array) - Segna la casella del campo come spuntata<br />**$extra** (*mixed*) - Attributi extra da aggiungere al tag come un array o una stringa letterale |
| **Ritorno**         | Un tag HTML di input di tipo checkbox                        |
| **Tipo di ritorno** | string                                                       |



Esempio:

```php+HTML
form_checkbox('newsletter', 'accept', TRUE);
```

L'esempio precedente creerebbe un campo di input di tipo select come il seguente:

```html
<input type="checkbox" name="newsletter" value="accept" checked="checked" />
```



------

`form_radio($data = '', $value = '', $checked = FALSE, $extra = '')`



Questa funzione è identica in tutto e per tutto alla funzione `form_checkbox()` di cui sopra, tranne che usa il tipo di input "radio".

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$data** (*array*) - Dati degli attributi del campo <br />**$value** (*string*) - Valore del campo di input<br />**$checked** (array) - Segna la casella del campo come spuntata<br />**$extra** (*mixed*) - Attributi extra da aggiungere al tag come un array o una stringa letterale |
| **Ritorno**         | Un tag HTML di input di tipo radio                           |
| **Tipo di ritorno** | string                                                       |



------

`form_submit($data = '', $value = '', $extra = '')`



Funzione che permette di generare un pulsante di invio del form standard.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$data** (*string*) - Nome del pulsante<br />**$value** (*string*) - Valore del pulsante<br />**$extra** (*mixed*) - Attributi extra da aggiungere al tag come un array o una stringa letterale |
| **Ritorno**         | Un tag HTML di input di tipo submit                          |
| **Tipo di ritorno** | string                                                       |



Esempio:

```php+HTML
form_submit('mysubmit', 'Invio');
```

L'esempio precedente creerebbe un campo di input di tipo submit come il seguente:

```html
<input type="submit" name="mysubmit" value="Invio" />
```



------

`form_reset($data = '', $value = '', $extra = '')`



Funzione che permette di generare un pulsante di reset standard. L'uso è identico a `form_submit()`.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$data** (*string*) - Nome del pulsante<br />**$value** (*string*) - Valore del pulsante<br />**$extra** (*mixed*) - Attributi extra da aggiungere al tag come un array o una stringa letterale |
| **Ritorno**         | Un tag HTML di input di tipo pulsante di reset               |
| **Tipo di ritorno** | string                                                       |



------

`form_button($data = '', $content = '', $extra = '')`



Funzione che permette di generare un pulsante standard. Puoi crearne uno passandogli minimamente il nome e il contenuto del pulsante nel primo e nel secondo parametro.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$data** (*string*) - Nome del pulsante<br />**$content** (*string*) - Label del pulsante<br />**$extra** (*mixed*) - Attributi extra da aggiungere al tag come un array o una stringa letterale |
| **Ritorno**         | Un tag HTML di tipo pulsante                                 |
| **Tipo di ritorno** | string                                                       |



Esempio:

```php+HTML
$data = array(
        'name'          => 'button',
        'id'            => 'button',
        'value'         => 'true',
        'type'          => 'reset',
        'content'       => 'Reset'
);

form_button($data);
```

L'esempio precedente creerebbe un campo di input di tipo submit come il seguente:

```html
<button name="button" id="button" value="true" type="reset">Reset</button>
```



------

`form_label($label_text = '', $id = '', $attributes = [])`



Funzione che permette di generare una Label.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$label_text** (*string*) - Testo della label<br />**$id** (*string*) - ID dell'elemento del form per cui stiamo creando un'etichetta<br />**$attributes** (*mixed*) - Attributi HTML |
| **Ritorno**         | Un tag HTML label di campo                                   |
| **Tipo di ritorno** | string                                                       |



Esempio:

```php+HTML
form_label('Username', 'username');
```

L'esempio precedente creerebbe un campo di input di tipo submit come il seguente:

```html
<label for="username">What is your Name</label>
```



------

`form_fieldset($legend_text = '', $attributes = [])`



Funzione che permette di generare campi fieldset/legend.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$legend_text** (*string*) - Testo da inserire nel tag `<legend>` <br />**$attributes** (*array*) - Attributi HTML da settare nel tag `<fieldset>` |
| **Ritorno**         | Un tag HTML di apertura fieldset                             |
| **Tipo di ritorno** | string                                                       |



Esempio:

```php+HTML
form_fieldset('Address Information');
"<p>fieldset content here</p>\n";
form_fieldset_close();
```

L'esempio precedente creerebbe un campo di input di tipo submit come il seguente:

```html
<fieldset>
	<legend>Address Information</legend>
    <p>fieldset content here</p>
</fieldset>
```



------

`form_fieldset_close($extra = '')`



Funzione che produce un tag di chiusura `</fieldset>`. L'unico vantaggio di usare questa funzione è che ti permette di passarle dei dati che saranno aggiunti sotto il tag.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$extra** (*string*) - Qualsiasi cosa da aggiungere dopo il tag di chiusura |
| **Ritorno**         | Un tag HTML di chiusura fieldset                             |
| **Tipo di ritorno** | string                                                       |



Esempio:

```php+HTML
$string = '</div></div>';
form_fieldset_close($string);
```

L'esempio precedente creerebbe un campo di input di tipo submit come il seguente:

```html
</fieldset>
</div> </div>
```



------

`form_close($extra = '')`



Produce un tag `</form>` di chiusura.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$extra** (*string*) - Qualsiasi cosa da aggiungere dopo il tag di chiusura |
| **Ritorno**         | Un tag HTML di chiusura del form                             |
| **Tipo di ritorno** | string                                                       |



Esempio:

```php+HTML
$string = '</div></div>';
form_close($string);
```

L'esempio precedente creerebbe un campo di input di tipo submit come il seguente:

```html
</form> 
</div></div>
```

