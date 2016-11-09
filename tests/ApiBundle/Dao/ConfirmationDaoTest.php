<?php
class ConfirmationDaoTest extends PHPUnit_Framework_TestCase
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
            '\ApiBundle\Dao\Confirmation', 
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
        
        $reflector = new ReflectionProperty('\ApiBundle\Dao\Confirmation', 'database');
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
                
        $confirm = [
            'id' => '1',
            'status_id' => '1',
            'action' => 'create',
            'code'  => 'a7d72j1l'
        ];
        
        $this->statementMock->expects($this->any())
            ->method('fetch')
            ->will($this->returnValue($confirm));
        
        $response = $this->mockDao->get(1);
        
        $this->assertEquals($response, $confirm, 'Confirms are not equals');

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
     * @return void
     */
    public function testGetByCode()
    {       
                
        $confirm = [
            'id' => '1',
            'status_id' => '1',
            'action' => 'create',
            'code'  => 'a7d72j1l'
        ];
        
        $this->statementMock->expects($this->any())
            ->method('fetch')
            ->will($this->returnValue($confirm));
        
        $response = $this->mockDao->getByCode('a7d72j1l');
        
        $this->assertEquals($response, $confirm, 'Confirms are not equals');

    }
    
    /**
     * Test
     *
     * @expectedException \Exception
     *
     * @return void
     */
    public function testGetByCodeException()
    {   
               
        $this->connectionMock->expects($this->any())
            ->method('executeQuery')
            ->will($this->throwException(new \Exception));

        
        $this->mockDao->getByCode('khhjds7kj3kjhasd8');

    }
    
    
    /**
     * Test
     *
     * @return void
     */
    public function testCreate()
    {       
                
        $response = $this->mockDao->create(2, 'create');
        
        $this->assertEquals(strlen($response), 8, 'The code lenght is incorrect');

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

        
        $this->mockDao->create('bad id', 'create');

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

}