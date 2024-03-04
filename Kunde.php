<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€


// to do: change name 'PageTemplate' throughout this file

require_once './Page.php';


class Kunde extends Page
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
        $orders = array();
        // to do: fetch data for this view from the database
		// to do: return array containing data
        if(isset($_SESSION["order_id"])) {
            $order_id = $_SESSION["order_id"];

            $sql = "SELECT * FROM ordered_article natural join article where ordering_id='$order_id'";

            $recordset = $this->_database->query($sql);
            if (!$recordset) {
                throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
            }
            // read selected records into result array

            $record = $recordset->fetch_assoc();
            while ($record) {
                $orders[] = $record;
                $record = $recordset->fetch_assoc();
            }
            return $orders;
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
     */
    protected function generateView():void
    {
        $orders = $this->getViewData(); //NOSONAR ignore unused $data
        $this->generatePageHeader('Kunde'); //to do: set optional parameters

        echo <<<EOT
        <body onload="startPolling() " >
        <nav>
            <ul class="navbar">
                <li><a href="order.php">Bestellung</a></li>
                <li><a href="#" class="selectedSite">Kunde</a></li>
                <li><a href="Backer.php">Bäcker</a></li>
                <li><a href="Fahrer.php">Fahrer</a></li>
            </ul>
        </nav>
        

        <div class="content">
            <section id="leftSide">
                 <h2>Meine Bestellungen</h2>
                  <div id="listContainer">
                    <ul id="myOrders" >
EOT;
        foreach ($orders as $order) {
            $order_id=$order["ordered_article_id"];
            $article=$order["article_id"];
            $status=$order["status"];
            $name=$order["name"];

            $strgSattus=$this->getStatus($status);


            echo <<<EOT
        <li class="item" data-order_id=$order_id data-name=$name data-status=$status> $name :  $strgSattus</li>
EOT;

        }

        echo <<<EOT
                        
                    </ul>
                </div>
        </section>
        </div>

        
EOT;

        // to do: output view of this page
        $this->generatePageFooter();
    }

    protected function getStatus($status)
    {
        switch ($status)
        {
            case 0: return "Bestellt";
            case 1: return "Im Offen";
            case 2: return "Fertig";
            case 3: return "Unterwegs";
            case 4: return "Geliefert";
        }
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
            $page = new Kunde();
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
Kunde::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >