<?php

namespace App\Http\Controllers;

use App\Models\CarCategories;
use App\Models\CarMake;
use App\Models\CarModel;
use App\Models\CarVersion;
use App\Models\CarVersionItem;
use Illuminate\Http\Request;
use Goutte\Client;
use Illuminate\Http\Response;
use Symfony\Component\HttpClient\HttpClient;


/**
 * LEMBRETE DE PEGAR A URL DE LINK DOS MODELOS.
 */

class ScrapingController extends Controller
{

    private $urlVersionSearch = 'https://www.autocosmos.cl/';

    private $filterMake = 'html body main div div div div div div ul li a';
    private $filterHref = 'html body main div div div div div div ul li a';
    private $filterModel = 'html body main div section div div article div div h3 a';
    private $filterHrefModels = 'html body main div section div div article div div h3 a';
    private $filterVersions = 'html body main section div div div table tbody tr td a';
    private $filterVersionsHref = 'html body main section div div div table tbody tr td a';
    private $filterVersionsFeatures = 'html body main article div section div div div table';
    private $filterVersionsPrice = 'html body main article section div p strong';

    public function __construct()
    {
        ini_set('max_execution_time', 7200);
    }


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
        $this->clearTables();
        $mainUrl = 'https://www.autocosmos.cl';

        $client  = new Client(HttpClient::create());

        $url = "https://www.autocosmos.cl/catalogo/busqueda/segmentos";

        $crawler = $client->request('GET', $url);
        $makes = $crawler->filter($this->filterMake)->each(function ($node) {
            return $node->text();
        });

               // Group by Categories x models
        $categories = CarCategories::all();

        // Begin foreach categories
        foreach ($categories as $categorie) {

            $categorieAlias = $categorie->alias;
            $categorieId = $categorie->id;

            $urlCategory = "https://www.autocosmos.cl/catalogo/busqueda/$categorieAlias/";

            $client  = new Client(HttpClient::create());
            $crawler = $client->request('GET', $urlCategory);
            $makes = $crawler->filter($this->filterMake)->each(function ($node) {
                return $node->text();
            });

            // get href for scraping href tag 'a'.
            $makes_href = $crawler->filter($this->filterHref)->each(function ($node) {
                return $node->attr('href');
            });

            // Group by models x makes
            // for ($i=1; $i<=count($makes_href); $i++) {
                foreach ($makes_href as $key => $makeHref) {

                $urlModels = "https://www.autocosmos.cl$makes_href[$key]";
                $client  = new Client(HttpClient::create());
                $crawler = $client->request('GET', $urlModels);

                $models = $crawler->filter($this->filterModel)->each(function ($node) {
                    return $node->text();
                });

                // get href Models from scraping
                $modelsHref = $crawler->filter($this->filterHrefModels)->each(function ($node) {
                    return $node->attr('href');
                });

                $carMake = new CarMake();

                $carMake->desc = $makes[$key];
                $carMake->alias = $makes[$key];
                $carMake->car_categories_id = $categorieId;
                $carMake->url_reference = $urlCategory;
                $carMake->save();

                // Group by models x versions
                foreach ($modelsHref as $key => $modelHref) {

                    $urlVersions = "https://www.autocosmos.cl$modelHref";

                    $client  = new Client(HttpClient::create());

                    $carModel = new CarModel();
                    $carModel->desc = $models[$key];
                    $carModel->alias = $models[$key];
                    $carModel->url_reference = $urlModels;
                    $carMake->models()->save($carModel);

                    $crawler = $client->request('GET', $urlVersions);
                    $versions = $crawler->filter($this->filterVersions)->each(function ($node) {
                        return $node->text();
                    });

                    // get href Models from scraping
                    $versionsHref = $crawler->filter($this->filterVersionsHref)->each(function ($node) {
                        return $node->attr('href');
                    });

                    foreach($versionsHref as $key => $versionHref){
                       $carVersion = new CarVersion();
                       $carVersion->desc = $versions[$key];
                       $carVersion->url_reference = $versions[$key];

                       $carModel->versions()->save($carVersion);

                       $urlVersionsItems = "https://www.autocosmos.cl$versionHref";

                       $crawler = $client->request('GET', $urlVersionsItems);
                        $tableVersion = $crawler->filter($this->filterVersionsFeatures)->filter('tr')->each(function ($tr, $i) {
                            return $tr->filter('td')->each(function ($td, $i) {
                                return trim($td->text());
                            });
                        });


                        $priceVersion = $crawler->filter($this->filterVersionsPrice)->each(function ($node) {
                            return $node->text();
                        });

                        $carVersionItem = new CarVersionItem();

                        if(count($tableVersion)>0){

                            $carVersionItem->fuel = $tableVersion[0][1];
                            $carVersionItem->width = $tableVersion[20][1];
                            $carVersionItem->height = $tableVersion[23][1];
                            $carVersionItem->traction = $tableVersion[14][1];
                            $carVersionItem->cc = $tableVersion[1][1];
                            $carVersionItem->doors = "5";
                            $carVersionItem->screen = "NI";
                            $carVersionItem->android = "NI";
                            $carVersionItem->air_bag = $tableVersion[50][1];
                            $carVersionItem->price =  count($priceVersion) == 0 ? '$0.00' : $priceVersion[0];
                            $carVersionItem->doors = $tableVersion[49][1];
                            $carVersionItem->abs = $tableVersion[47][1];
                            $carVersionItem->steering_wheel = $tableVersion[46][1];
                            $carVersionItem->air_cond = $tableVersion[30][1];
                            $carVersionItem->bluetooth = "NI";
                            $carVersionItem->tires = $tableVersion[16][1];
                            $carVersionItem->url_reference = $urlVersionsItems;

                            $carVersion->versions()->save($carVersionItem);

                        }
                    }

                }
            }
        }

        return response()->json(['data' => "Scraping Realizado com sucesso!!"]);
    }

    private function clearTables(){
        $carMake = new CarMake();
        $carModel = new CarModel();

        $carMake->delete();
        $carModel->delete();
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
