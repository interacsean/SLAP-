<?php

class SLAP_ {

    function get_page($p){

        if (empty($p)) $p = HOME_PAGE;

        // *** only [a-zA-z0-9_-] are allowed in filenames
        if (preg_match('/^[\w\-]+$/', $p) !== 1){
            http_response_code(403);
            return array('body'=>'Invalid pagename');
        }

        // *** check for attack OR file not found
        if (stripos($p, "http") !== false || stripos($p, '..') || !file_exists('../pages/'.$p.'.html')){
            http_response_code(404);

            // * only return body of this file to prevent preRenders etc
            return array(
                'body'=> file_exists('../pages/404.html')
                    ? file_get_contents('../pages/404.html')
                    : 'Page not found'
            );
        }

        // *** Check that file permissions are reasonable (<755)
        if (fileperms('../pages/'.$p.'.html') & 18){
            http_response_code(403);
            return array('body'=>'Invalid permissions for page');
        }

        // *** We should be good to go
        $page_raw = file_get_contents("../pages/".$p.".html");
        $fields = array('body','head','preRender');
        $page = array('name'=>$p);
        foreach($fields as $f){
            preg_match_all("/<!-- ?field:$f ?-->([\s\S]*?)<!-- ?end:$f ?-->/", $page_raw, $page_extract);

            if (count($page_extract[0]) > 0){
                $page[$f] = $page_extract[1][0];
            }
        }

        // var_export($page);
        return $page;
    }

    /**
    * TODO: rename this method
    *
    * @param  String
    * @return [type]       [description]
    */
    function do_page($pageName = null)
    {
        $page = $this->get_page(
            ( $pageName !== null   ?  $pageName :
            ( isset($_GET['page']) ?  $_GET['page'] :
                                      HOME_PAGE
            ) )
        );

        $inner_content = str_replace("<!-- BODY -->", $page['body'], file_get_contents('../templates/content.html'));

        // ob_start();

        // * pre-render instructions from page
        if (!empty($page['preRender'])){

            // yes you read it right... eval.  Power to the implementor!
            $replace = array();
            eval("function page_preRender(&\$replace){ ".$page['preRender']."}");
            page_preRender($replace);

            // $inner_content = ob_get_contents().$inner_content;
            // ob_clean();

            foreach($replace as $varName=>$code){
                $inner_content = str_replace("<!-- var:$varName -->", $code, $inner_content);
            }
        }

        // ** wrap if not ajax
        if (!isset($_GET['ajax'])){
            $content = str_replace("<!-- CONTENT -->", $inner_content, file_get_contents('../templates/page.html'));

            // !* highlight menu item
            // $content = preg_replace("/(<a href ?= ?['\"]\/".$page['name']."['\"])([^<]*)class[ ]?=[ ]?(['\"]))/i", "$1$2class=$3selected ", $content);
            //$content = preg_replace("/(<a href ?= ?['\"]\/".$page['name']."['\"][^<]*)>/i", "$1 class='selected'", $content);

        }else{
            $content = $inner_content;

        }

        return $content;
    }

}
