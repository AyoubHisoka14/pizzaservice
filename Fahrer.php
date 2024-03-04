<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€


// to do: change name 'PageTemplate' throughout this file


require_once './Page.php';


class Fahrer extends Page
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
    protected function getViewData(int $x=0):array
    {
        if(!$x)
        {
            $sql = "SELECT status, address, ordering_id, count(*) as number, sum(status) as result FROM ordered_article natural join ordering group by ordering_id";

            $recordset = $this->_database->query($sql);
            if (!$recordset) {
                throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
            }

            $orders = array();
            $record = $recordset->fetch_assoc();
            while ($record) {
                $orders[]=$record;
                $record = $recordset->fetch_assoc();
            }
            return $orders;
        }

        else
        {
            $sql = "SELECT name, price FROM ordered_article natural join article where ordering_id='$x'";

            $recordset = $this->_database->query($sql);
            if (!$recordset) {
                throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
            }

            $orders = array();
            $record = $recordset->fetch_assoc();
            while ($record) {
                $orders[]=$record;
                $record = $recordset->fetch_assoc();
            }
            return $orders;
        }


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
        $this->generatePageHeader('Fahrer'); //to do: set optional parameters

        echo <<<EOT
     <body >
     
         <script>
            // Reload the page every 5 seconds (5000 milliseconds)
            setInterval(function() {
                window.location.reload();
            }, 5000);
        </script>
        
        <nav>
            <ul class="navbar">
                <li><a href="order.php">Bestellung</a></li>
                <li><a href="Kunde.php">Kunde</a></li>
                <li><a href="Backer.php">Bäcker</a></li>
                <li ><a href="#" class="selectedSite">Fahrer</a></li>
            </ul>
        </nav>
        
        <div class="allOrders">
        
     EOT;

        foreach ($orders as $order)
        {
            $this->addOrder($order);

        }

        echo <<<EOT
    
    </div>
EOT;

        // to do: output view of this page
        $this->generatePageFooter();
    }

    private function addOrder($order)
    {
        $number=$order["number"];
        $result=$order["result"];
        $ordering_id=$order["ordering_id"];
        $address=$order["address"];
        $status=$order["status"];

        $address=htmlspecialchars($address);

        if(!($result/($number*2)>=1))   //We check if all Pizzas of an Order have the Status 2
        {
            return;
        }

        if($status==2)
        {
            echo <<<EOT
<form method="post" action="Fahrer.php" id=$ordering_id >
    <div >
        <fieldset>
            <legend>Bestellung $ordering_id </legend>
            <p>$address</p>
EOT;

            $orders2=$this->getViewData(intval($ordering_id));
            $totalPrice=0;
            foreach ($orders2 as $order)
            {
                $name=$order["name"];
                $price=$order["price"];
                $totalPrice+=$price;

                echo <<<EOT
            <span>$name - </span>
EOT;
            }
            echo <<<EOT
        <br>
        <p>$totalPrice €</p>
EOT;



echo <<<EOT

            <div class="radioButtons">
                <input type="hidden" name="ordering_id" value=$ordering_id>
                <label >
                    <input type="radio"  name=$ordering_id  value="2" onclick="submit('$ordering_id');" checked> Fertig
                </label>
        
                <label >
                    <input type="radio" name=$ordering_id value="3" onclick="submit('$ordering_id');"> Unterwegs
                </label>
        
                <label >
                    <input type="radio"  name=$ordering_id value="4" onclick="submit('$ordering_id');"> Geliefert
                </label>
            </div>
        </fieldset>
        <br>
</div>
</form>
EOT;
        }
        elseif ($status==3)
        {
            echo <<<EOT
<form method="post" action="Fahrer.php" id=$ordering_id >
    <div >
        <fieldset>
            <legend>Bestellung $ordering_id </legend>
            <p>$address</p>

EOT;

            $orders2=$this->getViewData(intval($ordering_id));
            $totalPrice=0;
            foreach ($orders2 as $order)
            {
                $name=$order["name"];
                $price=$order["price"];
                $totalPrice+=$price;

                echo <<<EOT
            <span>$name - </span>
EOT;
            }
            echo <<<EOT
        <br>
        <p>$totalPrice €</p>
EOT;



            echo <<<EOT
            <div class="radioButtons">
                <input type="hidden" name="ordering_id" value=$ordering_id>
                <label>
                    <input type="radio"  name=$ordering_id value="2" onclick="submit('$ordering_id');"> Fertig
                </label>
        
                <label >
                    <input type="radio" name=$ordering_id value="3" onclick="submit('$ordering_id');" checked> Unterwegs
                </label>
        
                <label>
                    <input type="radio"  name=$ordering_id value="4" onclick="submit('$ordering_id');"> Geliefert
                </label>
            </div>
        </fieldset>
        <br>
</div>
</form>
EOT;
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

        if(count($_POST))
        {

            if(isset($_POST["ordering_id"]))
            {
                $ordering_id=$_POST["ordering_id"];
                $status=$_POST[$ordering_id];

                $sqlid = $this->_database->real_escape_string($ordering_id);
                $sqlstatus = $this->_database->real_escape_string($status);

                if($status==4)
                {
                    $sql = "delete from ordering WHERE ordering_id='$sqlid' ";
                    $this->_database->query($sql);
                }
                else{
                    $sql = "UPDATE ordered_article SET status = '$sqlstatus' WHERE ordering_id='$sqlid' ";
                    $this->_database->query($sql);
                }



            }

            header('Location: Fahrer.php');
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
            $page = new Fahrer();
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
Fahrer::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >