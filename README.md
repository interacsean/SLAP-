# SLAP-

SLAP- ('Slap dash') is an adaptable, lightweight php/js nano-framework for punching out static sites fast.

It isn't the most robust thing in the world, but as most developers know (even if they won't admit it) as robustness increases, complexity increases exponentially.  SLAP- unapologetically sits to the far-simple side of the spectrum.

## TODO

[_] get HEAD working properly (how was it meant to work?!)
[_] menu classes and states

* jQuery optional: include jQuery and SLAP-jQuery.js will utilise jQuery's methods, lowering the filesize payload.  Not using jQuery?  Also include SLAP-vanilla.js and everything will still work

Purpose

* ajax load between pages
* menu state management
* page templating.  history state push support
* contact forms?


## Quick start

You will need

* Apache server running PHP 5.4+

## Orientation

### Pages

Pages can currently have the following tokens, which take the form of comment tags and operate in pairs in a wrapper fashion:

<!-- field:preRender --><!--end:preRender -->
<!-- field:head --><!--end:head --> 
<!-- field:content --><!--end:content -->

Between these comment tags, enter code for the following:

#### 'preRender' token field

This is OPTIONAL, and if included should be php code (no '<?php ?>' required) that will be executed before the page render.

A single variable is accessible in scope: $replace, which is an empty array.  In the 'content' section, you may include placeholders (more info below) which will be replaced with the value of the matching element in $replace array.  The key of the array element will be matched against the name of the placeholder 'tag'.

#### 'content' token field

This is HTML code that makes up the content portion of the page.  When a page is dynamically loaded, this content is loaded into the page template's <!-- CONTENT --> placeholder (via the content template, if one exists)).

#### 'head' token field

For HTML and scripts related to each page.  This code will be rendered into the <!-- HEAD --> placeholder of the page template on first render.

### 'pageLoad' token field

Javascript to be executed each time the page is loaded.

### example page



## Templates


## Features

* HTML5 ready.
* Asynchronous page loading
* (Currently) depends on jQuery being present.


## License

The code is available under the [MIT license](LICENSE.txt).


## Known bugs

* In the case of an inline link that uses SLAP- asyncronous page loading functionality, other links on the page (e.g. menu links) will not receive the .selected class to correctly indicate that link/page is active.