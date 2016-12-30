<?php

namespace Atpi\ManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DomCrawler\Crawler;
use Doctrine\Common\Cache\FilesystemCache;
use Symfony\Component\HttpFoundation\Request;
use Atpi\ManagementBundle\Entity\MidOfficeID as MidOfficeID;
use Symfony\Component\Filesystem\Filesystem;
class AjaxController extends Controller
{
    public $cacheDriver = false;
    protected $cachename_etsaccountcodelist = 'etsaccountcodelist';
    protected $cachename_etsMidOfficeIDList = 'etsMidOfficeIDList';




    /**
     * Open link to cache
     */
    public function openCache($path)
    {
        $this->cacheDriver = new FilesystemCache($this->container->get('kernel')->getCacheDir() . $path);
    }

    /**
     * Check for cache item
     * @param $requestHash
     * @return bool
     */
    public function checkCache($requestHash)
    {
        if ($this->cacheDriver->contains($requestHash)) {
            return $this->cacheDriver->fetch($requestHash);
        }
        return false;
    }



    public function importMidOfficeIDListAction(Request $request)
    {

        $all = $request->request->get('all');
        $openCache = $this->openCache('/management/MidOfficeID');
        $cachDir = $this->cacheDriver->getdirectory();

        if ($xml = $this->checkCache($this->cachename_etsMidOfficeIDList . 'xml')) {
            $simplexml = simplexml_load_string($xml);
        } else {
            $twsApi = $this->get('TwsApi');
            if ($request = $twsApi->getRequest('MidOfficeID')) {
                if ($response = $twsApi->get($request, 4800)) {
                    try {
                        $html = '';
                        $openCache;
                        $simplexml = &$response->simplexml;
                        $this->cacheDriver->save($this->cachename_etsMidOfficeIDList . 'xml', $response->simplexml->asXML());
                    } catch (\Exception $e) {

                        $html = 'Could not save response to cache';
                    }
                }
            } else {

                $html = 'Request not available';
            }
        }
        $count = 1;
        $data = array();
        $rs = $simplexml->xpath('//TWSResponse/data/searchResults/SET/row');
        $MidOfficeIDService = $this->get('MidOfficeID');

        foreach ($rs as $key => $value) {
            $MidOfficeIDService->createMidOfficeID(trim($value->midoffice));
            $data[] = (string)$value->midoffice;
            $count++;
        }
        if($all == '1'){
            $progress = 20;
            $nextRequest =$this->get('router')->generate('atpi_management_import_accountCodes_from_ets_ajax_requestlist');
        }else{
            $progress = 100;
            $nextRequest = "" ;
        }

        (new Filesystem)->remove($cachDir);
        return new JsonResponse(array(
            'success' => true,
            'progress' => $progress,
            'panelHeading' => 'MidOfficeIDs',
            'nextRequest' => $nextRequest,
            'nextRequestData' => array('MidOfficeIDs' => $data, 'counter' => '0'),
            'message' => ucfirst($this->get('translator')->trans('MidOfficeIDs import done'))));
    }

    public function importAccountCodeAction(Request $request)
    {
        set_time_limit(360);
        ini_set('memory_limit', '512M');
        $counter = $request->request->get('counter');
        $midOfficeIDs = $request->request->get('MidOfficeIDs');
        $midOfficeID = $request->request->get('MidOfficeID');
        if (!is_string($midOfficeIDs) && $midOfficeID != null) {

            $key = array_search($midOfficeID,$midOfficeIDs);
            $helper = array();
            for($key ; $key < count($midOfficeIDs); $key++){
                $helper[] = $midOfficeIDs[$key];

            }
            unset($midOfficeIDs);
            $midOfficeIDs =$helper;
        }
        if(is_string($midOfficeIDs)){$midOfficeIDs = array('0' =>$midOfficeIDs);}


        if ($counter < count($midOfficeIDs)) {

            $this->openCache('/management/accountCode');
            $currentMidOfficeID = $midOfficeIDs[$counter];

            if ($xml = $this->checkCache($this->cachename_etsaccountcodelist . $currentMidOfficeID . 'xml')) {

                $simplexml = simplexml_load_string($xml);

            } else {

                $twsApi = $this->get('TwsApi');
                if ($request = $twsApi->getRequest('AccountCodes')) {

                    $request->setParam('midOffice', $currentMidOfficeID);

                    if ($response = $twsApi->get($request, 4800)) {
                        try {

                            $html = '';
                            $this->openCache('/management/accountCode');
                            $simplexml = &$response->simplexml;
                            $this->cacheDriver->save($this->cachename_etsaccountcodelist . $currentMidOfficeID . 'xml', $response->simplexml->asXML());
                        } catch (\Exception $e) {

                            $html = 'Could not save response to cache';
                        }
                    }

                } else {

                    $html = 'Request not available';
                }
            }

            return new JsonResponse(array(
                'success' => true,
                'progress' => ((80 / count($midOfficeIDs)) * $counter + 1) + 20,
                'panelHeading' => $midOfficeIDs[$counter],
                'nextRequest' => $this->get('router')->generate('atpi_management_import_accountCodes_from_ets_ajax_requestlist'),
                'message' => $midOfficeIDs[$counter] . ' import done',
                'nextRequestData' => array('MidOfficeIDs' => $midOfficeIDs, 'counter' => ++$counter),
            ));


        } else {

            return new JsonResponse(array(
                'success' => true,
                'progress' => 100,
                'message' => ' import succes',
                'nextRequest' => $this->get('router')->generate('atpi_management_make_accountCode_ready_for_storage_Ajax'),
                'nextRequestData' => array('MidOfficeIDs' => $midOfficeIDs, 'counter' => 0),));
        }
    }


    public function MakeAccountCodeReadyForStorageAction(Request $request)
    {
        set_time_limit(360);
        ini_set ( 'memory_limit', '1028M' );
        $cacheAccountCodes =  $this->openCache('/management/accountCode');
        $cachAccountCodesDir = $this->cacheDriver->getdirectory();

        $counter = $request->request->get('counter');
        $midOfficeIDs = $request->request->get('MidOfficeIDs');
        if(is_string($midOfficeIDs)){$midOfficeIDs = array('0' =>$midOfficeIDs);}
        if ($counter < count($midOfficeIDs)) {
            $currentMidOfficeID = $midOfficeIDs[$counter];
            if ($xml = $this->checkCache($this->cachename_etsaccountcodelist . $currentMidOfficeID . 'xml')) {
                $simplexml = simplexml_load_string($xml);

                $rs = $simplexml->xpath('//TWSResponse/data/searchResults/SET/row/response/accountCodes/accountCode');

                $rsItem = current($rs);
                $count = 1;
                $page = 1;
                $totalCnt = count($rs);
                $collection = array();
                $this->openCache('/management/chopt/'.$currentMidOfficeID);
                while($rsItem) {

                    $collection[] = $rsItem->asXML();

                    if (($count % 200 == 0) || ($count == $totalCnt)) {

                        $this->cacheDriver->save($this->cachename_etsaccountcodelist . $page, $collection);
                        ++$page;
                        $collection = array();
                    }

                    ++$count;
                    $rsItem = next($rs);
                }
            }
            return new JsonResponse(array(
                'success' => true,
                'progress' => ((100 / count($midOfficeIDs)) * $counter + 1) ,
                'message' => ' accountcodes ready to store',
                'nextRequest' => $this->get('router')->generate('atpi_management_make_accountCode_ready_for_storage_Ajax'),
                'nextRequestData' => array('MidOfficeIDs' => $midOfficeIDs, 'counter' => ++$counter)));
        }else{
            (new Filesystem)->remove($cachAccountCodesDir);
            return new JsonResponse(array(
                'success' => true,
                'progress' => 100,
                'message' => ' Accountcodes are ready for storige',
                'nextRequest' => $this->get('router')->generate('atpi_management_store_accountCode_from_cache_Ajax'),
                'nextRequestData' => array('MidOfficeIDs' => $midOfficeIDs, 'counter' => 0, 'page' => 1),));
        }
    }

    public function storeAccountCodeFromCacheAction(Request $request)
    {

        set_time_limit(720);
        ini_set ( 'memory_limit', '1028M' );

        $counter = $request->request->get('counter');
        $midOfficeIDs = $request->request->get('MidOfficeIDs');
        if(is_string($midOfficeIDs)){$midOfficeIDs = array('0' =>$midOfficeIDs);}
        $midOfficeID = trim($request->request->get('MidOfficeID'));
        $page = $request->request->get('page');
        $pageToGo = $page;
        $pageMax = $request->request->get('pageMax');
        $currentMidOfficeID = null;
        if($midOfficeID){

            $counter = array_search($midOfficeID,$midOfficeIDs);
            $currentMidofficeID = $midOfficeID;
        }

        if ($counter < count($midOfficeIDs)) {
           if($currentMidOfficeID == null) $currentMidOfficeID = $midOfficeIDs[$counter];


            if($page == 1) {

                $pageMax = 0;
                $c = 0;
                if(file_exists ($this->container->get('kernel')->getCacheDir() . '/management/chopt/' . $currentMidOfficeID)) {
                    $dirs = scandir($this->container->get('kernel')->getCacheDir() . '/management/chopt/' . $currentMidOfficeID);
                    foreach ($dirs as $dir) {
                        if ($c >= 2) {
                            $pageMax = $pageMax + count(scandir($this->container->get('kernel')->getCacheDir() . '/management/chopt/' . $currentMidOfficeID . '/' . $dir)) - 2;
                        }
                        ++$c;
                    }
                }
            }

            if($pageMax >= $page && $pageMax != 0){

                $this->openCache('/management/chopt/'.$currentMidOfficeID);
                $result = $this->checkCache($this->cachename_etsaccountcodelist . $page);
                $AccountCodeService = $this->get('atpi.management.accountCode');

                foreach ($result as $value) {
                    $simplexml = simplexml_load_string($value);
                    $result = $simplexml->xpath('/accountCode');
                    $AccountCodeService->createAccountcode($result[0]);

                }

            }
            if($pageMax == $page || $pageMax == 0 ){

                ++$counter;
                $page =0;
            }

            return new JsonResponse(array(
                'success' => true,
                'progress' => ((100 / count($midOfficeIDs)) * $counter + 1) ,
                'message' => ' accountcodes from '.$currentMidOfficeID.' are storing. Page '.$pageToGo.' from: '.$pageMax  ,
                'nextRequest' => $this->get('router')->generate('atpi_management_store_accountCode_from_cache_Ajax'),
                'nextRequestData' => array('MidOfficeIDs' => $midOfficeIDs, 'counter' => $counter, 'page' => ++$page, 'pageMax' => $pageMax)));
        }else{
            $cache =  $this->openCache('/management/chopt/');
            (new Filesystem)->remove($this->cacheDriver->getdirectory());
            return new JsonResponse(array(
                'success' => true,
                'progress' => 100 ,
                'message' => ' Import and storage complete'));

        }
    }

}