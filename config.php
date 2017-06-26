<?php

// Self-explanatory, sorry.  No trailing slash.
define('BASE_URL', 'http://slap.dev');

// '/' for normal sites, '/sub-folder' if your site is not at the root level
define('BASE_DIR', '/');

// Sets the default page for requests to "/"
define('HOME_PAGE', 'home'); 

// _________________________________________

// If you're seeing this, it's because I'm developing on Windows 10 with Bash and I haven't jigged up the system to be have better permissions
define('IGNORE_FILE_PERMS', true);