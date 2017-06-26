<?php

class SLAP_ {

    /**
     * Return the page data that is stored in the relevant file in /pages/ 
     *
     * @param string      $p    Page name to get (matches file-name)
     *
     * @return array            Associative array with structured page information...
     */
    function get_page($p){

        if (empty($p)) $p = HOME_PAGE;

        // *** only [a-zA-z0-9_-] are allowed in filenames
        if (preg_match('/^[\w\-]+$/', $p) !== 1){
            http_response_code(403);
            return array('content'=>'Invalid page-name');
        }

        // *** check for attack OR file not found
        if (stripos($p, "http") !== false || stripos($p, '..') || !file_exists('../pages/'.$p.'.html')){
            http_response_code(404);

            // * only return content of this file to prevent preRenders etc
            return array(
                'content'=> file_exists('../pages/404.html') ? 
                    file_get_contents('../pages/404.html') :
                    'Page not found'
            );
        }

        // *** Check that file permissions are reasonable (<755)
        if ( (!defined('IGNORE_FILE_PERMS') || IGNORE_FILE_PERMS === false ) && ( fileperms('../pages/'.$p.'.html') & 18 )){
            http_response_code(403);
            return array('content'=>'Invalid permissions for page');
        }

        // *** We should be safe and good to go
        $page_raw = file_get_contents("../pages/".$p.".html");
        $fields = array('content','head','preRender', 'pageLoad');
        $page = array('name'=>$p);

        foreach($fields as $f){
            // *** get everything between the template placeholders
            preg_match_all("/<!-- ?field:$f ?-->([\s\S]*?)<!-- ?end:$f ?-->/", $page_raw, $page_extract);

            if (count($page_extract) > 0 && count($page_extract[0]) > 0){
                // *** there should only be one match for each placeholder... uses the first one.
                $page[$f] = $page_extract[1][0];
            }
        }

        return $page;
    }

    /**
    * Render the HTML for a given page, or the page parameter present in the query string
    *
    * @param  string    $pageName   Name of the page, if omitted, inspects the query string (this is the common behaviour)
    * @return string                The HTML to render
    */
    function slap_it($pageName = null)
    {
        $page = $this->get_page(
            ( $pageName !== null ?  
                $pageName :
                ( isset($_GET['page']) ?
                    $_GET['page'] :
                    HOME_PAGE
                ) 
            )
        );
        if (!array_key_exists('content', $page)){
            return false;
        }

        // *** optional additional wrapper template
        $inner_content = file_exists('../templates/content.html') ?
            str_replace("<!-- CONTENTINNER -->", $page['content'], file_get_contents('../templates/content.html')) :
            $page['content'] ;
        
        // *** pre-render instructions from page
        if ( array_key_exists('preRender', $page) && is_string($page['preRender']) && !empty($page['preRender']) ){

            // *** Yes you read it right... eval.  Full power to the implementor with this micro-framework!
            $replace = array();
            eval("function page_preRender(&\$replace){ ".$page['preRender']."}");
            page_preRender($replace);

            foreach($replace as $varName=>$code){
                $inner_content = str_replace("<!-- var:$varName -->", $code, $inner_content);
            }
        }

        // ** Technically the implementor could just whack a <script> tag at the end of their body...
        if ( array_key_exists('pageLoad', $page) && is_string($page['pageLoad']) && !empty($page['pageLoad']) ){
            $inner_content .= "<script id='SLAP-pageLoad-script'>\r\n".$page['pageLoad']."\r\n</script>";
        }

        // *** wrap into the page template if not an asyncronous / ajax request for the page
        if (!isset($_GET['ajax'])){

            $content = str_replace("<!-- CONTENT -->", 
                "<div id=\"SLAP-content\">\r\n".$inner_content."\r\n</div>", 
                file_get_contents('../templates/page.html'));

            // *** highlight currently selected menu item
            $content = preg_replace("/(<a href ?= ?['\"]\/".$pageName."['\"])([^>]*)class ?= ?(['\"])/i", "$1$2class=$3selected ", $content);
            // what if there is no 'class' attribute on the link element:
            //$content = preg_replace("/(<a href ?= ?['\"]\/".$pageName."['\"][^>]*)>/i", "$1 class='selected'", $content);

            if ( array_key_exists('head', $page) && is_string($page['head']) && !empty($page['head']) ){
                $content = str_replace("<!-- HEAD -->", $page['head'], $content);
            }
            
        }else{
            $content = $inner_content;

        }

        return $content;
    }

}
