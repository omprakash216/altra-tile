    <?php
    class DbConfig 
    {  
        //Localhost
        // public $_host = 'localhost';
        // private $_username = 'root';
        // private $_password = '';
        // private $_database = 'vanaya_spaces';



        public $_host = 'localhost';
        private $_username = 'root';
        private $_password = '';
        private $_database = 'u624194505_vanyaspaces1';
        
    	/*
    	private $_username = 'u739430485_gca';
    	private $_password = 'Aprajita@#4040';
    	private $_database = 'u739430485_gca';
    	*/
        
        public $connection;
        
        public function __construct()
        {
            if (!isset($this->connection)) {
                try {
                    // Try to connect with error reporting
                    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
                    $this->connection = new mysqli($this->_host, $this->_username, $this->_password, $this->_database);
                    
                    // Set charset to utf8mb4
                    $this->connection->set_charset("utf8mb4");
                } catch (mysqli_sql_exception $e) {
                    // Check if it's a connection refused error
                    if (strpos($e->getMessage(), 'refused') !== false || strpos($e->getMessage(), 'No connection could be made') !== false) {
                        $errorMessage = "MySQL Server is not running. Please start MySQL in XAMPP Control Panel.\n\n";
                        $errorMessage .= "Steps to fix:\n";
                        $errorMessage .= "1. Open XAMPP Control Panel\n";
                        $errorMessage .= "2. Click 'Start' button next to MySQL\n";
                        $errorMessage .= "3. Wait for MySQL to start (status should turn green)\n";
                        $errorMessage .= "4. Refresh this page\n\n";
                        $errorMessage .= "Connection Details:\n";
                        $errorMessage .= "Host: " . $this->_host . "\n";
                        $errorMessage .= "Database: " . $this->_database;
                        
                        error_log('Database connection failed: ' . $e->getMessage());
                        die("<div style='font-family: Arial, sans-serif; padding: 20px; max-width: 600px; margin: 50px auto; border: 2px solid #dc3545; border-radius: 8px; background: #f8d7da; color: #721c24;'>
                            <h2 style='color: #dc3545; margin-top: 0;'>⚠️ Database Connection Error</h2>
                            <p><strong>MySQL Server is not running!</strong></p>
                            <p>Please follow these steps to fix:</p>
                            <ol style='line-height: 1.8;'>
                                <li>Open <strong>XAMPP Control Panel</strong></li>
                                <li>Click the <strong>'Start'</strong> button next to <strong>MySQL</strong></li>
                                <li>Wait for MySQL to start (status should turn <span style='color: green; font-weight: bold;'>green</span>)</li>
                                <li>Refresh this page</li>
                            </ol>
                            <hr style='border-color: #dc3545; margin: 20px 0;'>
                            <p style='font-size: 0.9em; margin-bottom: 0;'><strong>Connection Details:</strong><br>
                            Host: <code>" . htmlspecialchars($this->_host) . "</code><br>
                            Database: <code>" . htmlspecialchars($this->_database) . "</code></p>
                        </div>");
                    } else {
                        // Other database errors
                        error_log('Database connection failed: ' . $e->getMessage());
                        throw new Exception('Database connection failed: ' . $e->getMessage());
                    }
                } catch (Exception $e) {
                    error_log('Database connection failed: ' . $e->getMessage());
                    throw new Exception('Database connection failed: ' . $e->getMessage());
                }
            }   
            
            return $this->connection;
        }
    }
    ?>