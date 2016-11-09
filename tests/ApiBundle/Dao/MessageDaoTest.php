<?php
class MessageDaoTest extends PHPUnit_Framework_TestCase
{
    
    protected $mockDao;
    protected $connectionMock;
    protected $statementMock;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void Nothing
     */
    protected function setUp()
    {
        $this->mockDao = $this->mocks(
            '\ApiBundle\Dao\Message', 
            null
        );
        
        $this->connectionMock = $this->mocks(
            'Doctrine\DBAL\Connection', 
            array('executeQuery', 'executeUpdate', 'lastInsertId')
        );
        
        $this->statementMock = $this->mocks(
            'Doctrine\DBAL\Statement', 
            array('fetch', 'fetchAll')
        );
        
        $this->connectionMock->expects($this->any())
            ->method('executeQuery')
            ->will($this->returnValue($this->statementMock));
        
        $reflector = new ReflectionProperty('\ApiBundle\Dao\Message', 'database');
        $reflector->setAccessible(true);
        $reflector->setValue($this->mockDao, $this->connectionMock);
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
    public function testGet()
    {       
                
        $message = [
            'id' => '1',
            'email' => 'test@test.com',
            'created_at' => '2016-11-09T13:03:07Z',
            'status' => 'Este es un mensaje'
        ];
        
        $this->statementMock->expects($this->any())
            ->method('fetch')
            ->will($this->returnValue($message));
        
        $response = $this->mockDao->get(1);
        
        $this->assertEquals($response, $message, 'Messages are not equals');

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
               
        $this->connectionMock->expects($this->any())
            ->method('executeQuery')
            ->will($this->throwException(new \Exception));

        
        $this->mockDao->get(3);

    }
    
    /**
     * Test
     * 
     * @dataProvider getAllProvider
     *
     * @return void
     */
    public function testGetAll($query)
    {       
                
        $messages = [
            [
                'id' => '1',
                'email' => 'test@test.com',
                'created_at' => '2016-11-09T13:03:07Z',
                'status' => 'Este es un mensaje'
            ],
            [
                'id' => '2',
                'email' => 'test2@test.com',
                'created_at' => '2016-11-09T13:05:07Z',
                'status' => 'Este es otro mensaje'
            ]
        ];
        
        $this->statementMock->expects($this->any())
            ->method('fetchAll')
            ->will($this->returnValue($messages));
        
        $response = $this->mockDao->getAll(1, 10, $query);
        
        $this->assertEquals(count($response), 2, 'Messages quantity fails');

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
               
        $this->connectionMock->expects($this->any())
            ->method('executeQuery')
            ->will($this->throwException(new \Exception));

        
        $this->mockDao->getAll(3, 4, '');

    }
    
    
    /**
     * Test
     *
     * @return void
     */
    public function testCreate()
    {       
                
        $this->connectionMock->expects($this->any())
            ->method('lastInsertId')
            ->will($this->returnValue(22));
        
        $response = $this->mockDao->create('email@correcto.com', 'mensaje');
        
        $this->assertEquals($response, 22, 'The ID is incorrect');

    }
    
    /**
     * Test
     *
     * @expectedException \Exception
     *
     * @return void
     */
    public function testCreateException()
    {   
               
        $this->connectionMock->expects($this->any())
            ->method('executeQuery')
            ->will($this->throwException(new \Exception));

        
        $this->mockDao->create('email@correcto.com', 'mensaje');

    }
    
    /**
     * Test
     *
     * @return void
     */
    public function testDelete()
    {       
                
        $this->connectionMock->expects($this->any())
            ->method('executeUpdate')
            ->will($this->returnValue(1));
        
        $response = $this->mockDao->delete(10);
        
        $this->assertEquals($response, 1, 'Affected rows incorrect');

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
               
        $this->connectionMock->expects($this->any())
            ->method('executeUpdate')
            ->will($this->throwException(new \Exception));

        
        $this->mockDao->delete('bad id');

    }
    
    /**
     * Test
     *
     * @return void
     */
    public function testApproveCreate()
    {       
                
        $this->connectionMock->expects($this->any())
            ->method('executeUpdate')
            ->will($this->returnValue(1));
        
        $response = $this->mockDao->approveCreate(10);
        
        $this->assertEquals($response, 1, 'Affected rows incorrect');

    }
    
    /**
     * Test
     *
     * @expectedException \Exception
     *
     * @return void
     */
    public function testApproveCreateException()
    {   
               
        $this->connectionMock->expects($this->any())
            ->method('executeUpdate')
            ->will($this->throwException(new \Exception));

        
        $this->mockDao->approveCreate('bad id');

    }
    
    
    /**
     * Data provider
     *
     * @return void
     */
    public function getAllProvider()
    {
        return array(
            array('mensaje'),
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