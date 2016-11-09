<?php
class MessageControllerTest extends PHPUnit_Framework_TestCase
{
    
    protected $requestMock;
    protected $containerMock;
    protected $mockController;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void Nothing
     */
    protected function setUp()
    {
        $this->requestMock = $this->mocks('Symfony\Component\HttpFoundation\Request', array('get'));
        $this->containerMock = $this->mocks(
            'Symfony\Component\DependencyInjection\Container', 
            array('getParameter')
        );
        
        $this->mockController = $this->mocks(
            '\ApiBundle\Controller\MessageController',
            array('get', 'validateData', 'sendMail')
        );
        
        $reflector = new ReflectionProperty('\ApiBundle\Controller\MessageController', 'container');
        $reflector->setAccessible(true);
        $reflector->setValue($this->mockController, $this->containerMock);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @return void Nothing
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Mocksckass mokea la clase
     *
     * @param string $class    nombre de la clase
     * @param array  $aMethods metodos de la clase
     *
     * @return mocked class
     */
    public function mocks($class, $aMethods=array())
    {
        //constructor de mocks
        $oclassMock = $this->getMockBuilder($class)
            ->setMethods($aMethods)
            ->disableOriginalConstructor()
            ->getMock();
        return $oclassMock;
    }



    /**
     * Test
     *
     * @return void
     */
    public function testGetOne()
    {       
        
        $mockDao = $this->mocks(
            '\ApiBundle\Dao\Message',
            array('get')
        );
        
        $mockDao->expects($this->any())
            ->method('get')
            ->will($this->returnValue([
                'id' => '1',
                'email' => 'test@test.com',
                'created_at' => '2016-11-09T13:03:07Z',
                'status' => 'Este es un mensaje'
            ]));

        $this->mockController->expects($this->any())
            ->method('get')            
            ->will($this->onConsecutiveCalls($mockDao, new \ApiBundle\Utils\StandardResponse));
        
        $response = $this->mockController->getOne($this->requestMock, 1);
        $stdMessage = json_decode($response->getContent());
        
        $this->assertEquals($stdMessage->email, 'test@test.com', 'The email is not matched');

    }
    
    /**
     * Test
     *
     * @expectedException \Exception
     *
     * @return void
     */
    public function testGetException()
    {   
        
        $mockDao = $this->mocks(
            '\ApiBundle\Dao\Message',
            array('get')
        );
        
        $mockDao->expects($this->any())
            ->method('get')
            ->will($this->returnValue(null));

        $this->mockController->expects($this->any())
            ->method('get')            
            ->will($this->returnValue($mockDao));
        
        $this->mockController->getOne($this->requestMock, 3);

    }
    
    
    /**
     * Test
     *
     * @return void
     */
    public function testGetAll()
    {        
        
        $mockDao = $this->mocks(
            '\ApiBundle\Dao\Message',
            array('getAll')
        );
        
        $mockDao->expects($this->any())
            ->method('getAll')
            ->will($this->returnValue([
                [
                    'id' => '1',
                    'email' => 'test@test.com',
                    'created_at' => '2016-11-09T13:03:07Z',
                    'status' => 'Este es un mensaje'
                ],
                [
                    'id' => '2',
                    'email' => 'test2@test.com',
                    'created_at' => '2016-11-09T13:05:02Z',
                    'status' => 'Este es otro'
                ]
            ]));

        $this->mockController->expects($this->any())
            ->method('get')            
            ->will($this->onConsecutiveCalls($mockDao, new \ApiBundle\Utils\StandardResponse));
        
        $reflector = new ReflectionProperty('\ApiBundle\Controller\MessageController', 'container');
        $reflector->setAccessible(true);
        $reflector->setValue($this->mockController, $this->containerMock);
        
        $response = $this->mockController->getAll($this->requestMock);
        $stdMessage = json_decode($response->getContent());
        
        $this->assertEquals(count($stdMessage), 2, 'The quantity is not equal');

    }
    
/**
     * Test
     *
     * @expectedException \Exception
     *
     * @return void
     */
    public function testGetAllException()
    {
       
        
        $mockDao = $this->mocks(
            '\ApiBundle\Dao\Message',
            array('getAll')
        );        
        
        
        $mockDao->expects($this->any())
            ->method('getAll')
            ->will($this->returnValue(null));

        $this->mockController->expects($this->any())
            ->method('get')            
            ->will($this->returnValue($mockDao));
        
        $this->mockController->getAll($this->requestMock);

    }
    
    
    /**
     * Test
     * 
     * @dataProvider addProvider
     *
     * @return void
     */
    public function testAdd($email)
    {       
        
        $mockDaoMessage = $this->mocks(
            '\ApiBundle\Dao\Message',
            array('create')
        );
        
        $mockDaoConfirmation = $this->mocks(
            '\ApiBundle\Dao\Confirmation',
            array('create')
        );
        
        $mockDaoMessage->expects($this->any())
            ->method('create')
            ->will($this->returnValue(1)
        );
        
        $mockDaoConfirmation->expects($this->any())
            ->method('create')
            ->will($this->returnValue('94df9232')
        );

        $this->mockController->expects($this->any())
            ->method('get')            
            ->will($this->onConsecutiveCalls($mockDaoConfirmation, $mockDaoMessage, new \ApiBundle\Utils\StandardResponse));
        
        $this->requestMock->expects($this->any())
            ->method('get')            
            ->will($this->onConsecutiveCalls($email, 'text message'));
        
        $response = $this->mockController->add($this->requestMock);
        $id = $response->getContent();
        
        $this->assertEquals($id, '1', 'The created ID is not the same');

    }
    
    /**
     * Test
     * 
     * @dataProvider deleteProvider
     *
     * @return void
     */
    public function testDelete($email)
    {       
        
        $mockDaoMessage = $this->mocks(
            '\ApiBundle\Dao\Message',
            array('delete', 'get')
        );
        
        $mockDaoConfirmation = $this->mocks(
            '\ApiBundle\Dao\Confirmation',
            array('create')
        );
       
        $mockDaoMessage->expects($this->any())
            ->method('get')
            ->will($this->returnValue([
                'id' => '1',
                'email' => $email,
                'created_at' => '2016-11-09T13:03:07Z',
                'status' => 'Este es un mensaje'
            ]));
        
        $mockDaoConfirmation->expects($this->any())
            ->method('create')
            ->will($this->returnValue('94df9232')
        );

        $this->mockController->expects($this->any())
            ->method('get')            
            ->will($this->onConsecutiveCalls($mockDaoConfirmation, $mockDaoMessage, new \ApiBundle\Utils\StandardResponse));
        
        
        $response = $this->mockController->delete($this->requestMock, 1);
        $stdMessage = json_decode($response->getContent());
        
        $this->assertEquals($stdMessage->email, $email, 'The email is not matched');

    }
    
    /**
     * Test
     * 
     * @expectedException \Exception
     *
     * @return void
     */
    public function testDeleteException()
    {       
        
        $mockDaoMessage = $this->mocks(
            '\ApiBundle\Dao\Message',
            array('delete', 'get')
        );
        
        $mockDaoConfirmation = $this->mocks(
            '\ApiBundle\Dao\Confirmation',
            array('create')
        );
       
        $mockDaoMessage->expects($this->any())
            ->method('get')
            ->will($this->returnValue(null));
        
        
        $this->mockController->expects($this->any())
            ->method('get')            
            ->will(
                $this->onConsecutiveCalls($mockDaoConfirmation, $mockDaoMessage, new \ApiBundle\Utils\StandardResponse)
              );        
        
        $this->mockController->delete($this->requestMock, 4);

    }
    
    /**
     * Test
     * 
     * @dataProvider confirmationProvider
     *
     * @return void
     */
    public function testConfirmation($action)
    {       
        
        $email = 'test@test.com';
        
        $mockDaoMessage = $this->mocks(
            '\ApiBundle\Dao\Message',
            array('delete', 'get', 'approveCreate')
        );
        
        $mockDaoConfirmation = $this->mocks(
            '\ApiBundle\Dao\Confirmation',
            array('getByCode', 'delete')
        );
       
        $mockDaoMessage->expects($this->any())
            ->method('get')
            ->will($this->returnValue([
                'id' => '1',
                'email' => $email,
                'created_at' => '2016-11-09T13:03:07Z',
                'status' => 'Este es un mensaje'
            ]));
       
        $mockDaoConfirmation->expects($this->any())
            ->method('getByCode')
            ->will($this->returnValue([
                'id' => '1',
                'status_id' => '1',
                'action' => $action
            ]));        

        $this->mockController->expects($this->any())
            ->method('get')            
            ->will($this->onConsecutiveCalls($mockDaoConfirmation, $mockDaoMessage, new \ApiBundle\Utils\StandardResponse));
        
        
        $response = $this->mockController->confirmation($this->requestMock, '94df9232');
        $stdMessage = json_decode($response->getContent());
        
        $this->assertEquals($stdMessage->email, $email, 'The email is not matched');

    }
    
    /**
     * Test
     * 
     * @dataProvider confirmationExceptionProvider
     * @expectedException \Exception
     *
     * @return void
     */
    public function testConfirmationException($message, $confirm)
    {       
        
        $email = 'test@test.com';
        
        $mockDaoMessage = $this->mocks(
            '\ApiBundle\Dao\Message',
            array('delete', 'get')
        );
        
        $mockDaoConfirmation = $this->mocks(
            '\ApiBundle\Dao\Confirmation',
            array('getByCode')
        );
       
        $mockDaoMessage->expects($this->any())
            ->method('get')
            ->will($this->returnValue($message));
       
        $mockDaoConfirmation->expects($this->any())
            ->method('getByCode')
            ->will($this->returnValue($confirm));        

        $this->mockController->expects($this->any())
            ->method('get')            
            ->will($this->onConsecutiveCalls($mockDaoConfirmation, $mockDaoMessage, new \ApiBundle\Utils\StandardResponse));
        
        
        $this->mockController->confirmation($this->requestMock, '94d12232');

    }
    
/**
     * Test
     * 
     * @return void
     */
    public function testValidate()
    {       
        
        $maxLength = 100;        
       
        $method = new ReflectionMethod('\ApiBundle\Controller\MessageController', 'validateData');
        $method->setAccessible(true);
        $result = $method->invoke($this->mockController, 'email@correcto.com', 'mensaje correcto', $maxLength);

    }
    
    /**
     * Test
     * 
     * @dataProvider validateExceptionProvider
     * @expectedException \Exception
     *
     * @return void
     */
    public function testValidateException($email, $text)
    {       
        
        $maxLength = 10;        
       
        $method = new ReflectionMethod('\ApiBundle\Controller\MessageController', 'validateData');
        $method->setAccessible(true);
        $result = $method->invoke($this->mockController, $email, $text, $maxLength);

    }
    
    
    /**
     * Data provider
     *
     * @return void
     */
    public function addProvider()
    {
        return array(
            array('test@test.com'),
            array(''),
        );
    }
    
    /**
     * Data provider
     *
     * @return void
     */
    public function deleteProvider()
    {
        return array(
            array('test@test.com'),
            array('Anonimous'),
        );
    }
    
    /**
     * Data provider
     *
     * @return void
     */
    public function confirmationProvider()
    {
        return array(
            array('create'),
            array('remove'),
        );
    }
    
    /**
     * Data provider
     *
     * @return void
     */
    public function validateExceptionProvider()
    {
        return array(
            array('email@incorrecto', ''),
            array('email@correcto.com', ''),
            array('email@correcto.com', 'el mensaje parece ser muy largo '),            
        );
    }
    
    /**
     * Data provider
     *
     * @return void
     */
    public function confirmationExceptionProvider()
    {
        return array(
            array(
                [
                    'id' => '1',
                    'email' => $email,
                    'created_at' => '2016-11-09T13:03:07Z',
                    'status' => 'Este es un mensaje'
                ],
                []
                
            ),
            array(
                [],
                [
                    'id' => '1',
                    'status_id' => '1',
                    'action' => 'create'
                ]               
            ),
        );
    }

}