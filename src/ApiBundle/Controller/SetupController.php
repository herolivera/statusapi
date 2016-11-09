<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Message Class
 *
 * @Route("/setup")
 */
class SetupController extends Controller
{

    /**
     * Create schema
     *
     * @param Request $request    HTTP Request
     *
     * @Route("")
     * @Method("get")
     *
     */
    public function run(Request $request)
    {
        //Get Dao instance
        $dao = $this->get("schema.dao");
        //Insert Message to confirm
        $dao->create();

        $response = [
            'message' => 'Success database creation'
        ];

        $stdResponse = $this->get('StandardResponse');
        return new Response($stdResponse->setResponse($response));
    }
}
