# SLAP-

SLAP- ('Slap dash') is an adaptable, lightweight php/js nano-framework for punching out static sites fast.

It isn't the most robust thing in the world, but as savvy developers know (even if they won't admit it) as robustness goes up, complexity increases exponentially.  SLAP- unapologetically sits to the simple side of the spectrum.

Purpose

* HTML5 ready (whatever that means, am-i-rite?)
* Asynchronous page loading
* Allows for basic server-side processing
* Automatic link/menu CSS state management

Also

* (Currently) depends on jQuery being present

## Quick start

You will need:

* Apache server running PHP 5.4+
* Apache's mod_rewrite module
* `/pages/*` files to have sensible permissions (roughly less than 755)
* Point your server `/` virtual-host to the `./your-project/public/` directory

## Orientation

The below files are what makes SLAP- tick (and most of the time you don't need to touch):
`/public/index.php`
`/public/.htaccess`
`/lib/SLAP-lib.php`
`/public/lib/SLAP-.js`

`/config.php` is there for you to configure based on your server set up.

`/templates/page.html` is where you want to do your overall site design.
`/templates/content.html` is a lower tier template, should you need it.  

`/pages/*` are where you should pop the content of each of your pages.  This gets magically loaded into /templates/page.html

`/public/` is where any publicly accessible files like javascript, css, images need to live.  You should point your webserver here too as the site root, too.

`/public/css/style.css` contains an example of how to alter the appearance of links which point to the currently viewed page.

Generally:

http://www.your-site.com/about will load `/pages/about.html` into the `/templates/page.html` template

### Pages

Pages are html files in the `/pages/` folder.  They can contain have the following tokens, which take the form of comment tags and operate in pairs in a wrapper fashion:

`<!-- field:preRender --><!--end:preRender -->`
`<!-- field:head --><!--end:head -->`
`<!-- field:content --><!--end:content -->`
`<!-- field:pageLoad --><!--end:pageLoad -->`

Here are the guidelines for each token:

#### The 'content' token field

This is HTML code that makes up the content portion of the page.  When a page is dynamically loaded, this content is loaded into the page template's `<!-- CONTENT -->` placeholder (via the optional content.html template).

If linking to other pages within content, use the below class to utilise the link-state management and asyncronous loading:

`<a href="/some-page" class="SLAP-link">Find out more</a>`

In this area you can specify further placeholders, which the preRender code can replace.  These are also HTML comments, and take the form: `<!-- var:myVarName -->`

#### The 'head' token field

For HTML and scripts related to each page.  This code will be rendered into the `<!-- HEAD -->` placeholder of the page template on first render.

If you want a different title tag on each page, here's a good place to put it.

#### The 'preRender' token field

OPTIONAL

PHP code (withOUT `<?php ?>` tags) that will be executed before the page render.

A single variable is accessible in scope: `$replace`, which is an empty array.  In the 'content' section, you may include placeholders (more info above) which will be replaced with the value of the matching element in `$replace` array.  The key of the array element will be matched against the name of the placeholder 'tag'.

### The 'pageLoad' token field

OPTIONAL

JavaScript to be executed each time the page is loaded.


### Example page file content

This is straight out of `/pages/home.html`

```
<!-- field:preRender -->
$replace['random-game'] = ['Twister', 'Connect 4', 'Russian Roulette'][rand(0,2)];
<!-- end:preRender -->

<!-- field:head -->
<title>SLAP- home</title>
<!-- end:head -->

<!-- field:content -->
<h3>Welcome.</h3>
<p>We've got fun and games.</p>
<p>Like <!-- var:random-game --></p>
<a href="about" class='SLAP-link'>Go to about</a>
<!-- end:content -->

<!-- field:pageLoad -->
alert('Welcome to the jungle - we\'ve got fun and games');
<!-- end:pageLoad -->
```

## Templates

There are two templates...

### page.html

This is the top-most template.  It should have your `<html>` and `<body>` tags, etc.

Needs to include `<!-- HEAD -->` and `<!-- CONTENT -->` for the content-replacement / template system to work.

In your html, you can use the below classes to leverage automatic management of link-states.

```
<nav class="SLAP-menu">
	<a href="/home">Home</a>
	<a href="/about">About</a>
</nav>
```
Or alternatively:

`<a href="/some-page" class="SLAP-link">Find out more</a>`


### content.html

This file is optional.

When content is asyncronously loaded, it is wrapped in the content.html template (if the template file exists). 

Needs to include `<!-- CONTENTINNER -->`

One reason you might want to put things in here, and not just in page.html is if you open any link in a new window with `/ajax/page-name`.  The content.html template content will show up in this popup.  Ok, it's not a very good reason but it's here anyway should you find a use for it.

In the future, I may add the ability to specify a second-level template for each page, if there are commonalities between some pages.

## License

The code is available under the [MIT license](LICENSE.txt).

~~Buy~~ *Steal* it, Use it, break it, fix it,
Trash it, change it, mail, upgrade it,

## Known bugs

* In the case of an inline link that uses SLAP- asyncronous page loading functionality, other links on the page (e.g. menu links) will not receive the .selected class to correctly indicate that link/page is active.

## Todo list

* jQuery optional: include jQuery and SLAP-jQuery.js will utilise jQuery's methods, lowering the filesize payload.  Not using jQuery?  Also include SLAP-vanilla.js and everything will still work
* Future extension: Incorporate contact forms
* Site-wide php pre-processing
* Manage 'title' tag a bit better (common title to append?)
* Allow users to add their own page-fields via the config.php, and hook into both slap_it and get_page with callbacks
