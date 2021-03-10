<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;

class ScrapingController extends Controller
{

    public function index()
    {
        $url = 'https://www.bcentral.cl/';
        $client  = new Client(HttpClient::create(['timeout' => 60]));
        $crawler = $client->request('GET', $url);
        $return = $crawler->filter('.portlet-content .datos-day .item p')->each(function ($node) {
            return $node->text();
        });
        $uf = str_replace("$", "", $return[1]);


        return response()->json($uf);
    }
}
