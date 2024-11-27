<?php

class CreatoriveCwMaker {
    const CREATORIVEAPIKEY = 'CREATORIVEAPIKEY';

    public static function create() {
        $title = 'Soccer WC 2022';
        $wordlist = file_get_contents('D:/Dev/java/cwworld/data/soccerwc22.txt');
        $wd = 20;$ht = 20;
        $subj = 'Soccer';
        self::newcw($title, $wordlist, $wd, $ht, $subj);
    }

    private static function newcw($title, $wordlist, $width, $height, $subj) {
        $apiKey = get_option(CreatoriveCwMaker::CREATORIVEAPIKEY);
//        if ($apiKey === null || $apiKey === false) {
//            echo(wp_kses_data('Creatorive API Key undefined. Please enter a valid value in the EV-Crosswords Settings'));
//        }
        $headers = array(
            'content-type' => 'application/json',
        );
        $body = json_encode(array(
            'cmd' => 'start', 'words' => $wordlist,
            'nm' => $title, 'wd' => $width, 'ht' => $height,
            'sj' => $subj, 'clkey' => 'cw/create',
            'apikey' => 'sdshhhdghsh'
        ));
        //$response = wp_remote_post( 'https://creatorive/', array(
        //            'body'        => $body,
        //            'headers'     => $headers,
        //        ) );
//        $response = wp_remote_post( 'http://localhost:8087/entrevcoding/creatorive/index.php', array(
//            'body'        => $body,
//            'headers'     => $headers,
//        ));
        $i = 5;
    }
}