<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€


// to do: change name 'PageTemplate' throughout this file


require_once 'Page.php';


class order extends Page
{
    // to do: declare reference variables for members 
    // representing substructures/blocks

    /**
     * Instantiates members (to be defined above).
     * Calls the constructor of the parent i.e. page class.
     * So, the database connection is established.
     * @throws Exception
     */
    protected function __construct()
    {
        parent::__construct();
        // to do: instantiate members representing substructures/blocks
    }

    /**
     * Cleans up whatever is needed.
     * Calls the destructor of the parent i.e. page class.
     * So, the database connection is closed.
     */
    public function __destruct()
    {
        parent::__destruct();
    }

    /**
     * Fetch all data that is necessary for later output.
     * Data is returned in an array e.g. as associative array.
	 * @return array An array containing the requested data. 
	 * This may be a normal array, an empty array or an associative array.
     */
    protected function getViewData():array
    {
        $sql = "SELECT * FROM article ";

        $recordset = $this->_database->query($sql);
        if (!$recordset) {
            throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
        }
// read selected records into result array
        $orders = array();
        $record = $recordset->fetch_assoc();
        while ($record) {
            $orders[]=$record;
            $record = $recordset->fetch_assoc();
        }
        return $orders;
    }

    /**
     * First the required data is fetched and then the HTML is
     * assembled for output. i.e. the header is generated, the content
     * of the page ("view") is inserted and -if available- the content of
     * all views contained is generated.
     * Finally, the footer is added.
     * @return void
     * @throws Exception
     */
    protected function generateView():void
    {
        $orders = $this->getViewData(); //NOSONAR ignore unused $data
        $this->generatePageHeader('Bestellung'); //to do: set optional parameters

        echo <<<EOT
        <body onload="deleteAll();">
        <nav>
            <ul class="navbar">
                <li ><a href="#" class="selectedSite">Bestellung</a></li>
                <li><a href="Kunde.php">Kunde</a></li>
                <li><a href="Backer.php">Bäcker</a></li>
                <li><a href="Fahrer.php">Fahrer</a></li>
            </ul>
        </nav>
        

        <div class="content">
            <div id="leftSide">
        <!-- Dynamic content will be inserted here -->
        EOT;

        foreach ($orders as $order) {
            $this->addPizza($order);

        }

        echo <<<EOT
            </div>
            
            <div id="rightSide">
    
            <h2>Warenkorb</h2>
    
            <div id="listContainer">
                <ul id="itemList" onclick="selectItem()">
                    
                </ul>
            </div>              
                    <p  id="totalPrice"  class="texts" >Endpreis: <span>0.00</span> €</p>
                <div class="buttonDiv">
                    <button  onclick="deleteSelectedItems()" class="button">Pizza Löschen</button>
                    <button  onclick="deleteAll()" class="button">Alles Löschen</button>                                                   
                </div>
                
                <br> <br>
                <div>
                <form method="post" id="orderForm" action="order.php">
                    <label for="adress" class="texts">Adresse </br>
                        <input type="text" id="adress" name="adress" class="address">  </br> </br>                      
                    </label>
                    <div class="buttonDiv">
                    <input type="submit" id="submitButton" value="Bestellen" class="button" disabled>
                    <input type="reset" value="Löschen" class="button" onclick="SubmitButton(0)">
                    </div>
                </form>
                </div>
        </div>
    </div>
EOT;





        // to do: output view of this page
        $this->generatePageFooter();
    }
    private function addPizza($order)
    {
        $name=htmlspecialchars($order['name']);
        $price=htmlspecialchars($order['price']);
        $picture=htmlspecialchars($order['picture']);
        $id=htmlspecialchars($order['article_id']);
        echo <<<EOT
        
        <div class="imageContainer">
            <img src=$picture class="pic" data-price=$price data-name=$name data-articleId=$id alt="Pizza $name" onclick="imageClicked(event)">
            <p class="texts"> $name - $price €</p>
            
        </div>
        
EOT;

    }

    /**
     * Processes the data that comes via GET or POST.
     * If this page is supposed to do something with submitted
     * data do it here.
	 * @return void
     */
    protected function processReceivedData():void
    {
        parent::processReceivedData();
        // to do: call processReceivedData() for all members
        if(count($_POST)) {
            if (isset($_POST["adress"])) {
                $adress = $_POST["adress"];
                $sqladress = $this->_database->real_escape_string($adress);

                $sql = "INSERT INTO ordering (address) VALUES ('$sqladress')";
                $this->_database->query($sql);
                $order_id = $this->_database->insert_id;

                $_SESSION["order_id"]=$order_id;    //To set the id of the last Order of this Session

                foreach ($_POST as $key => $value) {

                    if (strpos($key, 'item') === 0) {
                        if (isset($value)) {
                            $sqlid = $this->_database->real_escape_string($value);
                            $sql = "INSERT INTO ordered_article (ordering_id, article_id) VALUES ('$order_id', '$sqlid')";
                            $this->_database->query($sql);
                        }

                    }
                }
            }

            header('Location: Kunde.php');
            die();
        }

    }

    /**
     * This main-function has the only purpose to create an instance
     * of the class and to get all the things going.
     * I.e. the operations of the class are called to produce
     * the output of the HTML-file.
     * The name "main" is no keyword for php. It is just used to
     * indicate that function as the central starting point.
     * To make it simpler this is a static function. That is you can simply
     * call it without first creating an instance of the class.
	 * @return void
     */
    public static function main():void
    {
        try {
            session_start();
            $page = new order();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            //header("Content-type: text/plain; charset=UTF-8");
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

// This call is starting the creation of the page. 
// That is input is processed and output is created.
order::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >