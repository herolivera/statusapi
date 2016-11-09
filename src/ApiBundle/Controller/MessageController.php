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
 * @Route("/api/status")
 */
class MessageController extends Controller
{
    
    /**
     * Get one
     *
     * @param Request $request HTTP Request
     * @param int     $id      ID
     *
     * @Route("/{id}")
     * @Method("get")
     *
     * @ApiDoc(
     *  section="Message",
     *  description="Add a user Message",
     *  parameters={
     *  }
     * )
     *
     */
    public function getOne(Request $request, $id)
    {
        
        $messageDao = $this->get("message.dao");
        
        //Insert Message
        $message = $messageDao->get($id);
        
        //Only if the email was sended
        if (!isset($message['id'])) {
            throw new \Exception('status messge not found', 400000);
        }

        $stdResponse = $this->get('StandardResponse');
        return new Response($stdResponse->setResponse($message));
    }
    
    /**
     * Paginate records
     *
     * @param Request $request    HTTP Request
     *
     * @Route("")
     * @Method("get")
     *
     * @ApiDoc(
     *  section="Message",
     *  description="Add a user Message",
     *  parameters={
     *       {"name"="p", "dataType"="int", "required"="false", "description"="Page"},
     *       {"name"="r", "dataType"="int", "required"="false", "description"="Rows per page"},
     *       {"name"="q", "dataType"="string", "required"="false", "description"="Query"},
     *  }
     * )
     *
     */
    public function getAll(Request $request)
    {
        $page = trim($request->get('p')) ? trim($request->get('p')) : 1;
        $rows = trim($request->get('r')) ? trim($request->get('r')) : $this->container->getParameter('rows_per_page');
        $query = trim($request->get('q'));
        
        $messageDao = $this->get("message.dao");
        
        //Insert Message
        $results = $messageDao->getAll($page, $rows, $query);
        
        //Only if the email was sended
        if (!count($results)) {
            throw new \Exception('status messge not found', 400000);
        }

        $stdResponse = $this->get('StandardResponse');
        return new Response($stdResponse->setResponse($results));
    }
    

    /**
     * Add user Message
     *
     * @param Request $request    HTTP Request
     *
     * @Route("")
     * @Method("post")
     *
     * @ApiDoc(
     *  section="Message",
     *  description="Add a user Message",
     *  parameters={
     *       {"name"="status", "dataType"="string", "required"="true", "description"="Status"},
     *       {"name"="email", "dataType"="string", "required"="true", "description"="Email"},     *
     *  }
     * )
     *
     */
    public function add(Request $request)
    {
        $email = trim($request->get('email'));
        $status = trim($request->get('status'));
        $maxLength = $this->container->getParameter('message_size');
        
        //Validate data first
        $this->validateData($email, $status, $maxLength);

        //Get Dao instances
        $confirmationDao = $this->get("confirmation.dao");
        $messageDao = $this->get("message.dao");
        
        //Insert Message
        $id = $messageDao->create($email, $status);
        
        //Only if the email was sended
        if ($email != '') {
            $confirmationNumber = $confirmationDao->create($id, 'create');
            $this->sendMail('create', $confirmationNumber, $email);
        }

        $stdResponse = $this->get('StandardResponse');
        return new Response($stdResponse->setResponse($id));
    }

   

    /**
     * Delete message
     *
     * @param Request $request    HTTP Request
     * @param int     $id         User ID
     *
     * @Route("/{id}")
     * @Method("delete")
     *
     * @ApiDoc(
     *  section="Message",
     *  description="Delete a message",
     *  parameters={
     *  }
     * )
     *
     */
    public function delete(Request $request, $id)
    {
        //Get Dao instances
        $confirmationDao = $this->get("confirmation.dao");
        $messageDao = $this->get("message.dao");
        
        //get the message
        $message = $messageDao->get($id);
        if (!isset($message['id'])) {
            throw new \Exception('status messge not found', 400000);
        }
        
        //Send confirmation if the email is not empty or delete directly
        if ($message['email'] != 'Anonimous') {
            //Insert Message to confirm
            $confirmationNumber = $confirmationDao->create($id, 'remove');
            $this->sendMail('remove', $confirmationNumber, $message['email']);
        } else {
            $messageDao->delete($message['id']);
        }

        $stdResponse = $this->get('StandardResponse');
        return new Response($stdResponse->setResponse(['email' => $message['email']]));
    }
    
    /**
     * Delete message
     *
     * @param Request $request    HTTP Request
     * @param int     $id         User ID
     *
     * @Route("/confirmation/{code}")
     * @Method("get")
     *
     * @ApiDoc(
     *  section="Message",
     *  description="Confirm a message",
     *  parameters={
     *  }
     * )
     *
     */
    public function confirmation(Request $request, $code)
    {
        //Get Dao instances
        $confirmationDao = $this->get("confirmation.dao");
        $messageDao = $this->get("message.dao");
        
        //get the confirmation
        $confirm = $confirmationDao->getByCode($code);
        if (!isset($confirm['action'])) {
            throw new \Exception('status messge not found', 400000);
        }
        
        //get the message
        $message = $messageDao->get($confirm['status_id']);
        if (!isset($message['id'])) {
            throw new \Exception('status messge not found', 400000);
        }
        
        if ($confirm['action'] == 'create') {
            //Update status
            $messageDao->approveCreate($message['id']);
        } else {
            //Remove the record
            $messageDao->delete($message['id']);
        }
        
        //Remove anyway the confirmation
        $confirmationDao->delete($confirm['id']);

        $stdResponse = $this->get('StandardResponse');
        return new Response($stdResponse->setResponse(['email' => $message['email']]));
    }

    
    /**
     * Validate email address
     *
     * @param string $email     Email address
     * @param string $status    Status Message
     * @param int    $maxLength Max Length message
     *
     * @return void
     * @throws Exception
     */
    protected function validateData($email, $status, $maxLength)
    {

        //This 3 error code were not in RAML, so I put it starting in 400010
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('invalid email addres', 400010);
        }
        
        if (trim($status) == "") {
            throw new \Exception('status message is empty', 400011);
        }

        if (strlen($status) > $maxLength) {
            throw new \Exception('status message is to long', 400012);
        }
    }
    
    /**
     * Send mail method
     *
     * @param string $action ACTION
     * @param string $code   CODE
     * @param string $email  EMAIL
     *
     * @codeCoverageIgnore
     *
     * @return void
     */
    protected function sendMail($action, $code, $email)
    {
        $from = $this->container->getParameter('email_from');
        $fromName = $this->container->getParameter('email_from_name');
        $baseURL = $this->container->getParameter('base_url');
        
        $url = "$baseURL/status/confirmation/$code";
        
        $mailer = $this->get('mailer');
        $message = $this->get('swift_message_factory')
            ->setContentType("text/html")
            ->setSubject(ucfirst($action).' message confirmation')
            ->setFrom($from, $fromName)
            ->setTo($email)
            ->setBody(
                $this->renderView(
                    'ApiBundle:Email:'.$action.'.html.twig',
                    array(
                        'code' => $code,
                        'email' =>  $email,
                        'url' => $url
                    )
                )
            );

        $mailer->send($message);
    }
}
