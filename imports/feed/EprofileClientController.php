<?php

namespace ApiBundle\Controller;

use OAuthServerBundle\OAuthServerBundle;
use steevanb\SSH2Bundle\Exception\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

// Symfony request and respons handeler
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Session\Session;

//Entetiies
use CoreBundle\Entity\User as User;
use CoreBundle\Entity\Product as Product;
use CoreBundle\Entity\Application as Application;
use CoreBundle\Entity\Configuration as Configuration;
use CoreBundle\Entity\Parameter as Parameter;
use CoreBundle\Entity\UserRelation as UserRelation;

//Monolog
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use OAuthServerBundle\Controller\EncryptionController as EncryptionController;


/**
 * Class EprofileClientController
 * @package ApiBundle\Controller
 * @Route ("/api")
 */
class EprofileClientController extends Controller
{

    /**
     * @var string
     */
    protected $data;

    /**
     * @var array
     */
    protected $userRelationFields = array(
        'UniqueCorporateGuid' => 'parent',
        'UniqueProfileGuid' => 'child'
    );

    /**
     * @var array
     */
    protected $userFields = array(
        'UniqueCorporateGuid' => 'uniqueCorporateGuid',
        'UniqueProfileGuid' => 'uniqueProfileGuid',
        'UserName' => 'username',
        'Email' => 'email',
        'Password' => 'password',
        'Active' => 'enabled'
    );

    /**
     * @var array
     */
    protected $productFields = array(
        'UniqueProductGuid' => 'uniqueProductGuid',
        'ProductName' => 'name'
    );
    /**
     * @var string
     */
    protected $configurationField = 'ApplicationUserCredentials';

    /**
     * @var array
     */
    protected $applicationsFields = array(
        'UniqueApplicationGuid' => 'uniqueApplicationGuid',
        'ApplicationName' => 'name'
    );

    /**
     * @var array
     */
    protected $defaultUserApplicationFields = array(
        'active' => '1',
        'favorite' => '0',
        'position' => '0',
        'purchased' => '0',
        'online_purchase' => '0',
        'subscribed' => '0'


    );

    /**
     * @var array
     */
    protected $defaultApplicationFields = array(
        'manageble' => 'a:3:{s:4:"icon";s:10:"theprofile";s:15:"applicationLogo";s:7:"atpicon";s:15:"applicationName";s:0:"";}',
        'integration_route' => 'dynamicLogin_dashboard',
//        'intergration' => '1',
//        'initial_state' => '0',
    );

    /**
     * @var array
     */
    protected $httpErrorCodes = array(
        100 => "Continue",
        101 => "Switching Protocols",
        200 => "Success",
        201 => "Created",
        202 => "Accepted",
        203 => "Non-Authoritative Information",
        204 => "No Content",
        205 => "Reset Content",
        206 => "Partial Content",
        300 => "Multiple Choices",
        301 => "Moved Permanently",
        302 => "Found",
        303 => "See Other",
        304 => "Not Modified",
        305 => "Use Proxy",
        306 => "(Unused)",
        307 => "Temporary Redirect",
        400 => "Bad Request",
        401 => "Unauthorized",
        402 => "Payment Required",
        403 => "Forbidden",
        404 => "Uri Not Found",
        405 => "Method Not Allowed",
        406 => "Not Acceptable",
        407 => "Proxy Authentication Required",
        408 => "Request Timeout",
        409 => "Conflict",
        410 => "Gone",
        411 => "Length Required",
        412 => "Precondition Failed",
        413 => "Request Entity Too Large",
        414 => "Request-URI Too Long",
        415 => "Unsupported Media Type",
        416 => "Requested Range Not Satisfiable",
        417 => "Expectation Failed",
        500 => "Internal Server Error",
        501 => "Not Implemented",
        502 => "Bad Gateway",
        503 => "Service Unavailable",
        504 => "Gateway Timeout",
        505 => "HTTP Version Not Supported",
    );

    /**
     * @var array
     */
    protected $message = array();

    /**
     * @var
     */
    protected $uniqueProfileGuid;

    /**
     * @var EncryptionController
     */
    protected $encryption;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager|object
     */
    protected $em;

    /**
     * @var \Monolog\Logger
     */
    protected $log;

    /**
     * TestController constructor.
     */
    public function __construct()
    {
        // create a log channel
        $this->log = new Logger('EprofileLog');
        $this->log->pushHandler(new StreamHandler('EprofileLog.log', Logger::WARNING));

        $this->encryption = new \OAuthServerBundle\Controller\EncryptionController();
    }

    /**
     * @Route ("/import", name="api_import")
     * @method ({ "POST", "GET"})
     */
    public function updateAction(Request $request)
    {
        if ($request->getMethod() != 'POST') {
            $this->log->error('No POST methode', array(405 => $this->httpErrorCodes[405]));
            $this->returnError(405);
        }

        $postMethod = $request->isMethod(Request::METHOD_POST) ? 'request' : 'query';

        if (empty($request->$postMethod->get('access_token'))) {
            $this->log->error('Access token empty', array(401 => $this->httpErrorCodes[405]));
            $this->returnError(401);
         }

        if (empty($data = $request->$postMethod->get('data'))) {
            $this->log->error('Data not found in request',array('204' => $this->returnError(204)));
            $this->returnError(204);
        }

        $this->log = new Logger('EprofileLog');
        $this->log->pushHandler(new StreamHandler('EprofileLog.log', Logger::WARNING));

        $this->data =json_decode($data);

        if ($this->storeUser()) {
            $this->log->info('User data found', array('200', $this->returnError(200)));

        } else {
            $this->log->error('Something is wrong with the script AppBundle:EprofilerClientController.php',array('406', $this->returnError(406)));
        }

        return new Response(json_encode(array('Message' => 'Import complete')));
    }

    /**
     * @return bool
     */
    protected function storeUser()
    {
        $temp = array();
        $this->em = $this->getDoctrine()->getManager();

        foreach ($this->data as  $UserCredentialsResult) {
            foreach ($this->userFields as $key => $value) {
                if ($value == 'password') {
                    $temp[$value] = $this->handleCredentialEncoding($UserCredentialsResult->$key);
                } elseif ($value == 'enabled') {
                    $temp[$value] = ($UserCredentialsResult->$key ? 1 : 0);

                } else {
                    $temp[$value] = $UserCredentialsResult->$key;
                }
            }

            if (!$User = $this->em->getRepository('CoreBundle:User')->findOneBy(array('uniqueProfileGuid' => $UserCredentialsResult->UniqueProfileGuid))) {
                $User = new User();
            }

                $User = $User->buildFromArray($temp);
            try{

                $this->em->persist($User);
                $this->em->flush();
                $this->em->refresh($User);

                $this->uniqueProfileGuid = $User->getUniqueProfileGuid();
                $this->bindParentUserToUser($UserCredentialsResult);
                $this->storeProduct($UserCredentialsResult->ProductCredentials);

            }catch(Exception $e ){
                $this->log->error('cant store user:',array('UniqueProfileGuid:'=> $User->getUniqueProfileGuid(),'Caught exception:'=> $e));
            }
        }
        return true;
    }

    /**
     * @param $ProductsCredentials
     */
    protected function storeProduct($ProductsCredentials)
    {
        $this->em = $this->getDoctrine()->getManager();

        foreach ($ProductsCredentials as $ProductCredentials) {

            $temp = array();

            foreach ($this->productFields as $key => $value) {
                $temp[$value] = $ProductCredentials->$key;
            }

            $Product = $this->em->getRepository('CoreBundle:Product')->findOneBy(array('uniqueProductGuid' => $ProductCredentials->UniqueProductGuid));

            try{
               if (!$Product) {
                   $Product = new Product();
                   $Product->buildFromArray($temp);

                   $this->em->persist($Product);
                   $this->em->flush();
               } else {
                   $Product->buildFromArray($temp);

                   $this->em->persist($Product);
                   $this->em->flush();
               }
               $this->storeApplication($ProductCredentials->ApplicationCredentials, $Product);
            }catch (Exception $e){
                $this->log->error('cant store Product:',array('UniqueProfileGuid User:'=>  $this->uniqueProfileGuid,
                    'UniqueProfileGuid Product:' => $ProductCredentials->UniqueProductGuid,
                    'Caught exception:'=> $e));
            }
        }
        return true;
    }

    /**
     * @param $ApplicationsCredentials
     */
    protected function storeApplication($ApplicationsCredentials, $Product)
    {

        foreach ($ApplicationsCredentials as $ApplicationCredentials) {
            $temp = array();

            foreach ($this->applicationsFields as $key => $value) {
                $temp[$value] = $ApplicationCredentials->$key;
            }

            if (!$Application = $this->em->getRepository('CoreBundle:Application')->findOneBy(array('uniqueApplicationGuid' => $ApplicationCredentials->UniqueApplicationGuid))) {
                $Application = new Application();
            }

            $bool = 0;

            foreach ($Product->getApplications() as $ProductApplication) {
                if ($Application->getUniqueApplicationGuid() == $ProductApplication->getUniqueApplicationGuid()) {
                    $bool = 1;
                }
            }

            try{
                if ($bool == 1) {
                    $Application->buildFromArray($temp);

                    $this->em->persist($Application);
                    $this->em->flush();
                } else {
                    $Application->buildFromArray($temp, $Product);
                    $Product->addApplication($Application);

                    $this->em->persist($Product);
                    $this->em->persist($Application);
                    $this->em->flush();
                }
            }catch (Exception $e){
                $this->log->error('cant store Application:',array('UniqueProfileGuid User:'=>  $this->uniqueProfileGuid,
                    'UniqueProfileGuid Application:' => $ProductApplication->UniqueProductGuid,
                    'Caught exception:'=> $e));
            }

        }
        $this->storeParameters($ApplicationsCredentials);
        return true;
    }

    /**
     * @param $ApplicationsCredentials
     */
    protected function storeParameters($ApplicationsCredentials)
    {
        foreach ($ApplicationsCredentials as $ApplicationCredentials) {

            $StoredApplication = $this->em->getRepository('CoreBundle:Application')->findOneBy(array('uniqueApplicationGuid' => $ApplicationCredentials->UniqueApplicationGuid));

            foreach ($ApplicationCredentials as $key => $value) {
                if (!array_key_exists($key, $this->applicationsFields)) {
                    $newParameter = new Parameter();
                    $newParameter->setName($key);

                    if (is_array($value) || is_object($value)) {

                        $newParameter->setValue(serialize($value));
                    } else {

                        $newParameter->setValue($value);
                    }

                    $found = false;
                    if (!empty($StoredApplication->getParameters())) {

                        foreach ($StoredApplication->getParameters() as $Parameter) {
                            if ($newParameter->getName() == $Parameter->getName()) {
                                $Parameter->setValue($newParameter->getValue());

                                try {
                                    $this->em->persist($Parameter);
                                    $this->em->flush();
                                }catch(Exception $e){
                                    $this->log->error('cant update Parameter:',array('UniqueProfileGuid User:'=>  $this->uniqueProfileGuid,
                                        'UniqueProfileGuid Application:' => $ApplicationCredentials->UniqueApplicationGuid,
                                        'Parameter id' => $Parameter->getId(),
                                        'Caught exception:' => $e));
                                }

                                $found = true;

                                if ($Parameter->getName() == $this->configurationField) {
                                    $this->storeConfiguration($ApplicationCredentials, $Parameter, $StoredApplication);
                                }
                                break;
                            }
                        }
                    }
                    if ($found == false) {
                        $newParameter->setApplication($StoredApplication);
                        $StoredApplication->addParameter($newParameter);

                        try {
                            $this->em->persist($StoredApplication);
                            $this->em->persist($newParameter);
                            $this->em->flush();
                        }catch(Exception $e){
                            $this->log->error('cant stored Parameter:',array('UniqueProfileGuid User:'=>  $this->uniqueProfileGuid,
                                'UniqueProfileGuid Application:' => $ApplicationCredentials->UniqueApplicationGuid,
                                'Parameter name' => $Parameter->getName(),
                                'Caught exception:' => $e));
                        }

                        if ($newParameter->getName() == $this->configurationField) {
                            $this->storeConfiguration($ApplicationCredentials, $newParameter, $StoredApplication);
                        }
                    }
                }
            }
            $this->setDefaultParameters($StoredApplication, $this->defaultApplicationFields);
        }
        return true;
    }


    /**
     * @param $ApplicationCredentials
     * @param $Parameter
     * @param $Application
     */
    protected function storeConfiguration($ApplicationCredentials, $Parameter, $Application)
    {
        $User = $this->em->getRepository('CoreBundle:User')->findOneBy(array('uniqueProfileGuid' => $this->uniqueProfileGuid));

        if ($Configuration = $this->em->getRepository('CoreBundle:Configuration')->findOneBy(array('user' => $User, 'parameter' => $Parameter, 'application' => $Application))) {

            try{
                $Configuration->setValue(serialize($ApplicationCredentials->ApplicationUserCredentials));
                $this->em->persist($Configuration);
                $this->em->flush();
            }catch (Exception $e){
                $this->log->error('cant update $Configuration:',array('UniqueProfileGuid User:'=>  $this->uniqueProfileGuid,
                    'UniqueProfileGuid Application:' => $ApplicationCredentials->UniqueApplicationGuid,
                    'Parameter id' => $Configuration->getId(),
                    'Caught exception:' => $e));
            }

        } else {
            $Configuration = new Configuration();
            $Configuration->setValue(serialize($ApplicationCredentials->ApplicationUserCredentials));
            $Configuration->setUser($User);
            $Configuration->setApplication($Application);
            $Configuration->setParameter($Parameter);

            try{
                $this->em->persist($Configuration);
                $this->em->flush();
            }catch (Exception $e){
                $this->log->error('cant store $Configuration:',array('UniqueProfileGuid User:'=>  $this->uniqueProfileGuid,
                    'UniqueProfileGuid Application:' => $ApplicationCredentials->UniqueApplicationGuid,
                    'Parameter name' => $Configuration->getName(),
                    'Caught exception:' => $e));
            }
        }
        $found = false;

        foreach ($User->getApplications() as $UserApplication) {
            if ($UserApplication->getName() == $Application->getName()) {
                $found = true;
            }
        }

        if ($found == false && $ApplicationCredentials->ApplicationEnabled) {
            $this->userToApplication($User, $Application);
        }

        if ($ApplicationCredentials->ApplicationEnabled) {
            $this->setDefaultUserConfiguration($User, $Application);
        }
        return true;
    }

    /**
     * @param $User
     * @param $Application
     */
    protected function userToApplication($User, $Application)
    {
        try{
            $User->addApplication($Application);
            $this->em->persist($User);
            $this->em->flush();
        } catch (Exception $e){
            $this->log->error('cant bind user to application:',array('User id:'=>  $User->getId,
                'application id:' => $Application->getId(),
                'Caught exception:' => $e));
        }
        return true;
    }

    /**
     * @param Application $StoredApplication
     * @param array $parameters
     */
    protected function setDefaultParameters(Application $StoredApplication, array $parameters)
    {
        foreach ($parameters as $key => $value) {
            $found = false;
            foreach ($StoredApplication->getParameters() as $Parameter) {
                if ($Parameter->getName() == $key) {
                    $found = true;
                }
            }
            if ($found == false) {

                    $Parameter = new Parameter();
                    $Parameter->setName($key);
                    $Parameter->setValue($value);
                    $Parameter->setApplication($StoredApplication);
                try {
                    $this->em->persist($Parameter);
                    $this->em->flush();
                } catch (Exception $e) {

                    $this->log->error('Cant set Default parameter:', array('UniqueProfileGuid User:'=>  $this->uniqueProfileGuid,
                        'parameter name:' => $Parameter->getName(),
                        'Caught exception:' => $e));
                }
            }
        }
        return true;
    }

    /**
     * @param User $User
     * @param Application $Application
     */
    protected function setDefaultUserConfiguration(User $User, Application $Application)
    {
        $this->setDefaultParameters($Application, $this->defaultUserApplicationFields);
        $this->em->refresh($Application);

        foreach ($this->defaultUserApplicationFields as $key => $value) {
            foreach ($Application->getParameters() as $Parameter) {
                if ($Parameter->getName() == $key) {

                    $Configuration = $this->em->getRepository('CoreBundle:Configuration')->findOneBy(array('user' => $User, 'parameter' => $Parameter, 'application' => $Application));
                    if ($Configuration == NULL) {
                        $Configuration = new Configuration();
                        $Configuration->setValue($value);
                        $Configuration->setUser($User);
                        $Configuration->setApplication($Application);
                        $Configuration->setParameter($Parameter);

                        try{
                            $this->em->persist($Configuration);
                            $this->em->flush();
                        } catch (Exception $e) {

                            $this->log->error('Cant store Configuration :', array('UniqueProfileGuid User:'=>  $this->uniqueProfileGuid,
                                'Application id:' => $Application->getId(),
                                'User id:' => $User->getId(),
                                'Caught exception:' => $e));
                        }
                    }
                    break;
                }
            }
        }
        return true;
    }

    /**
     * @param $UserData
     */
    protected function bindParentUserToUser($UserData)
    {
        $userArray = array();
        foreach ($this->userRelationFields as $key => $value) {
            if (isset($UserData->$key)) {
                if ($User = $this->em->getRepository('CoreBundle:User')->findOneBy(array('uniqueProfileGuid' => $UserData->$key))) {
                    $userArray[$value] = $User;
                } else {
                    $User = new User();
                    $User->setUniqueProfileGuid($UserData->$key);

                     try{
                        $this->em->persist($User);
                        $this->em->flush();
                        $userArray[$value] = $User;
                    } catch (Exception $e) {

                        $this->log->error('Cant store Parent User :', array('UniqueProfileGuid User:'=>  $this->uniqueProfileGuid,
                            'Parent user guid:' => $User->getUniqueProfileGuid(),
                            'Caught exception:' => $e));
                    }
                }
            }
        }
        if (!$UserRelation = $this->em->getRepository('CoreBundle:UserRelation')->findOneBy(array('parent' => $userArray['parent'], 'child' => $userArray['child']))) {
            $UserRelation = new UserRelation();
            foreach ($userArray as $key => $value) {
                $UserRelation->{'set' . $key}($value);
            }
            try {
                $this->em->persist($UserRelation);
                $this->em->flush();
            } catch(Exception $e){
                $this->log->error('Cant bind child user to parrent Parent User :', array('UniqueProfileGuid User:'=>  $this->uniqueProfileGuid,
                    'Caught exception:' => $e));
            }
        }
        return true;
    }

    /**
     * @return string
     */
    protected function handleCredentialEncoding($cypherText)
    {
        return $this->encryption->decryptMessage($cypherText);
    }

    /**
     * @param $code
     * @return Response
     */
    protected function returnError($code, $data = null)
    {
        if ($data) {
            $this->message = array(
                $code => $data,
            );
        } else {
            $this->message = array(
                $code => $this->httpErrorCodes[$code]
            );
        }

        return new Response(json_encode($this->message, false));
    }

}