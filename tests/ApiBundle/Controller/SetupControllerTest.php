<?php
class SetupControllerTest extends PHPUnit_Framework_TestCase
{
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void Nothing
     */
    protected function setUp()
    {
       
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
    public function testRun()
    {       
        $mockController = $this->mocks(
            '\ApiBundle\Controller\SetupController',
            array('get')
        );
        
        $mockDao = $this->mocks(
            '\ApiBundle\Dao\Schema',
            array('create')
        );
        
        $requestMock = $this->mocks('Symfony\Component\HttpFoundation\Request', array('get'));
                
        $mockController->expects($this->any())
            ->method('get')            
            ->will($this->onConsecutiveCalls($mockDao, new \ApiBundle\Utils\StandardResponse));
        
        $response = $mockController->run($requestMock);
        $stdMessage = json_decode($response->getContent());
        
        $this->assertEquals($stdMessage->message, 'Success database creation', 'Error matching message');

    }
    

}