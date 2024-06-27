# Classe Template Engine

**Riferimento path sorgente classe upload:** *core/System/Layout.php*



Il framework mette a disposizione un template engine php per gestire tutto ciò che riguarda il template.



#### Direttive del template engine:

- `Direttiva Extends`
- `Visualizzazione dei dati`
- `Template engine & JavaScript Frameworks`
- `Direttiva If`
- `Cicli`
- `Commenti`
- `Direttiva PHP`
- `Direttiva Include`



## Riferimenti della classe:

Per iniziare, diamo un'occhiata a un semplice esempio. Per prima cosa, esamineremo un layout di pagina "principale", quello definito come master template:

```php+HTML
<!-- Stored in app/Themes/admin/layout/master.blade.php -->
<html>
    <head>
        <title>Pat OS - @yield('title')</title>
        @yield(css)
    </head>
    <body>
        {{-- Testata --}}
    	{% include layout/partials/file_name %}
    	
    	{{-- Barra Navigazione sinistra --}}
    	{% include layout/partials/file_name %}

        <div class="container">
            @yield('content')
        </div>
        
        {{-- Footer --}}
    	{% include layout/partials/footer %}
        
        @yield(javascript)
    </body>
</html>
```

La direttiva `block`, come dice il nome, definisce un blocco di contenuto, mentre la direttiva `@yield` è usata per visualizzare il contenuto di una data sezione.

#### Estendere un layout

Quando si definisce una vista figlia, si può utilizzare la direttiva del template engine `extends` per specificare quale layout la vista figlia dovrebbe "ereditare".

```
<!-- Stored in app/Themes/admin/esempio/esempio.blade.php -->

{% extends layout/master %}

{% block content %}

    @parent

    <p>Sezione appesa alla master sidebar.</p>
    
{% endblock %}

```



#### Visualizzazione dei dati

Si possono visualizzare i dati passati alle viste avvolgendo la variabile tra parentesi graffe. Per esempio, si può visualizzare il contenuto di una variabile nome in questo modo:

```php
Hello, {{ $name }}.
```

Naturalmente, non si è limitati a visualizzare il contenuto delle variabili passate alla vista. Si può anche fare l'echo dei risultati di qualsiasi funzione PHP e delle funzioni delle librerie caricate:

```php
{{ anchor('anchor/this', 'link') }}
```



### Template engine & JavaScript Frameworks

Poiché molti framework JavaScript usano anche le parentesi graffe per indicare che una data espressione dovrebbe essere visualizzata nel browser, si può usare il simbolo `@` per informare il motore di rendering  che un'espressione non dovrebbe essere modificata. Per esempio:

```php+HTML
<h1>Slice Library</h1>

Hello, @{{ username }}
```

Nell'esempio di sopra, il simbolo @ sarà rimosso dal template engine; tuttavia, l'espressione {{ username }} rimarrà inalterata dal motore di template, permettendo invece di essere renderizzata dal framework JavaScript.



#### Direttiva If

Si possono costruire dichiarazioni **if** usando le direttive `@if`, `@elseif`, `@else` e `@endif`. Queste direttive funzionano in modo identico alle loro controparti PHP:

```php
@if (count($records) === 1)
    Un record presente!
@elseif (count($records) > 1)
    Più record presenti!
@else
    Nessun record presente!
@endif
```

Il template engine del framework fornisce anche una direttiva `@unless`:

```php
@unless (count($users) != 0)
    Non ci sono utenti da mostrare.
@endunless
```



#### Cicli

Il template engine del framework fornisce semplici direttive per lavorare con le strutture di loop di PHP. Di nuovo, ognuna di queste direttive funziona in modo identico alle loro controparti PHP:

```php
@for ($i = 0; $i < 10; $i++)
    Il valore corrente è {{ $i }}
@endfor

@foreach ($users as $key => $user)
    <p>Utente {{ $user }}</p>
@endforeach

@while (true)
    <p>Ciclo infinito.</p>
@endwhile
```

Quando si usano i cicli si può anche terminare il ciclo o saltare l'iterazione corrente:

```php
@foreach ($users as $key => $user)
    @if ($user == 'Mario')
        @continue
    @endif

    <li>{{ $user }}</li>

    @if ($key == 2)
        @break
    @endif
@endforeach
```

Si può anche includere la condizione con la dichiarazione della direttiva in una sola riga:

```php
@foreach ($users as $key => $user)
    @continue($key == 1)

    <li>{{ $user }}</li>

    @break($key == 4)
@endforeach
```



#### Commenti

Il template engine del framework permette anche di definire dei commenti nelle viste. Tuttavia, a differenza dei commenti HTML, i commenti del template engine non sono inclusi nell'HTML restituito dal' applicazione:

```php+HTML
{{-- Questo commento non verrà incluso nel HTML renderizzato --}}
```



#### Direttiva php

In alcune situazioni, è utile incorporare del codice PHP nelle viste dell'applicazione. Si può usare la direttiva del template engine del framework `@php` per eseguire un blocco di semplice PHP all'interno del template:

```php+HTML
@php
    [omissis]
@endphp
```



#### Inclusione di sotto viste

La direttiva `include` del template engine del framework permette di includere una vista dall'interno di un'altra vista. Tutte le variabili che sono disponibili alla vista padre saranno rese disponibili alla vista inclusa:

```html
<div>
    {% include esempio/include_general_data %}

    <div>
        <!-- Div Contenuti -->
    </div>
</div>
```

Anche se la vista inclusa erediterà tutti i dati disponibili nella vista padre, si può anche passare un array di dati extra alla vista inclusa.

