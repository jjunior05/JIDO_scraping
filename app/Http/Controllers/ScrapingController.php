<?php

namespace App\Http\Controllers;

use App\Models\CarCategories;
use App\Models\CarMake;
use App\Models\CarModel;
use App\Models\CarVersion;
use Illuminate\Http\Request;
use Goutte\Client;
use Illuminate\Http\Response;
use Symfony\Component\HttpClient\HttpClient;


/**
 * LEMBRETE DE PEGAR A URL DE LINK DOS MODELOS.
 */

class ScrapingController extends Controller
{
    private $filterMake = '.filters__item ul li';
    private $filterModel = '.listing-container article .model-versions-card__model';
    private $filterSrc = '.filters__item ul li a';
    private $filterVersions = '';

    public function getCotacaoMoeda()
    {
        $url = 'https://www.bcentral.cl/';
        $client  = new Client(HttpClient::create());
        $crawler = $client->request('GET', $url);
        $return = $crawler->filter('.portlet-content .datos-day .item p')->each(function ($node) {
            return $node->text();
        });
        $uf = str_replace("$", "", $return[1]);


        return response()->json(['data' => $return]);
    }

    public function getCarModels()
    {

        $mainUrl = 'https://www.autocosmos.cl';


        $client  = new Client(HttpClient::create());
        // $url = 'https://www.autocosmos.cl/catalogo/busqueda/sedan/honda/civic';
        // $client  = new Client(HttpClient::create(['timeout' => 60]));
        // $crawler = $client->request('GET', $url);
        // $return = $crawler->filter('.model-versions-card__collapse li p')->each(function ($node) {
        //     return $node->text();
        // });

        // Get all models
        $url = "https://www.autocosmos.cl/catalogo/busqueda/segmentos";


        $crawler = $client->request('GET', $url);
        $makes = $crawler->filter($this->filterMake)->each(function ($node) {
            return $node->text();
        });

        // Group by Categories x models
        $categories = CarCategories::all();
        foreach ($categories as $categorie) {

            $categorieAlias = $categorie->alias;
            $categorieId = $categorie->id;

            $urlModels = "https://www.autocosmos.cl/catalogo/busqueda/$categorieAlias/";

            $client  = new Client(HttpClient::create());
            $crawler = $client->request('GET', $urlModels);
            $makes = $crawler->filter($this->filterSrc)->each(function ($node) {
                return $node->text();
            });
            return response()->json(['data' => $makes]);
            // Group by models x makes
            foreach ($makes as $make) {

                $urlModels = "https://www.autocosmos.cl/catalogo/busqueda/segmentos/$make";
                $client  = new Client(HttpClient::create());
                $crawler = $client->request('GET', $urlModels);
                $models = $crawler->filter($this->filterModel)->each(function ($node) {
                    return $node->text();
                });
                $carMake = new CarMake();
                $carMake->desc = $make;
                $carMake->alias = $make;
                $carMake->car_categories_id = $categorieId;
                $carMake->save();

                // // Group by models x versions
                // foreach ($models as $model) {

                //     $urlVersions = "https://www.autocosmos.cl/catalogo/vigente/$make/$model";

                //     $client  = new Client(HttpClient::create());
                //     $crawler = $client->request('GET', $urlVersions);
                //     $versions = $crawler->filter($this->filterModel)->each(function ($node) {
                //         return $node->text();
                //     });
                //     $carModel = new CarModel();
                //     $carModel->desc = $model;
                //     $carModel->alias = $model;
                //     $carMake->models()->save($carModel);

                //     foreach ($versions as $version) {

                //         $carVersion = new CarVersion();
                //         $carVersion->desc = $version;

                //         $carModel->versions()->save($carVersion);
                //     }
                // }
            }
        }


        return response()->json(['data' => $models]);
    }



    public function getCarVersions()
    {
        // $url = 'https://www.autocosmos.cl/catalogo/busqueda/sedan/honda/civic';
        // $client  = new Client(HttpClient::create(['timeout' => 60]));
        // $crawler = $client->request('GET', $url);
        // $return = $crawler->filter('.model-versions-card__collapse li p')->each(function ($node) {
        //     return $node->text();
        // });

        $url = "https://www.autocosmos.cl/catalogo/vigente/chevrolet/captiva";



        $client  = new Client(HttpClient::create());
        $crawler = $client->request('GET', $url);
        $return = $crawler->filter('.table tr td a')->each(function ($node) {
            return $node->text();
        });


        return response()->json(['data' => $return]);
    }

    public function saveModelVersions()
    {
        $model = new CarModel();
        $model->desc = 'Teste Model';
        $model->alias = 'Teste Model 1';
        $model->car_makes_id = 4;

        $model->save();

        $version = new CarVersion();
        $version->desc = 'Teste version model';


        $model->versions()->save($version);

        return response()->json(['data' => $model], Response::HTTP_CREATED);
    }

    public function getCarCategories()
    {
        $categories = CarCategories::all();
        return response()->json($categories);
    }
}
