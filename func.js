
function addItem(text) {
    "use strict";
    const itemList = document.getElementById('itemList');
    const newItem = document.createElement('li');
    newItem.className = 'item';
    newItem.textContent = text;
    itemList.appendChild(newItem);
}

function selectItem() {
    "use strict";
    const target = event.target;

    if (target.tagName === 'LI') {
        target.classList.toggle('selected');
    }
}

function deleteSelectedItems() {
    "use strict";
    const itemList = document.getElementById('itemList');
    const selectedItems = itemList.querySelectorAll('.selected');

    selectedItems.forEach(item => {
        item.remove();
    });
    setPrice();

    if(!itemList.firstChild)
    {
        SubmitButton(0);
    }

}
function deleteAll() {
    "use strict";
        const itemList = document.getElementById('itemList');
        while (itemList.firstChild) {
            itemList.firstChild.remove();
        }
        setPrice();
        SubmitButton(0);

}

function imageClicked(event) {
    "use strict";
    // Accessing attributes of the clicked image
    const clickedImage = event.target;
    const name = clickedImage.getAttribute('data-name');
    const price = clickedImage.getAttribute('data-price');
    const id=clickedImage.getAttribute('data-articleId');

    const itemList = document.getElementById('itemList');
    const newItem = document.createElement('li');
    newItem.className = 'item';
    newItem.textContent = name;

    newItem.setAttribute('data-name', name);
    newItem.setAttribute('data-price', price);
    newItem.setAttribute('data-articleId', id);

    itemList.appendChild(newItem);
    setPrice();

    const inputField = document.getElementById('adress');
    if(inputField.value.trim() === "")
    {
        SubmitButton(0);
    }
    else {
        SubmitButton(1);
    }



}

function setPrice()
{
    "use strict";
    const itemList = document.getElementById('itemList');
    const totalPriceElement = document.querySelector('#totalPrice span');

    let totalPrice = 0;

    // Iterate through each list item
    itemList.querySelectorAll('li').forEach(item => {
        // Extract the price attribute value and convert it to a number
        const price = parseFloat(item.getAttribute('data-price'));

        // Add the price to the total
        totalPrice += price;
    });

    // Update the text content of the total price element
    totalPriceElement.textContent = totalPrice.toFixed(2); // Format the total price to two decimal places
}

function SubmitButton(x)
{
    "use strict";
    const button = document.getElementById("submitButton");

    button.disabled = !x;
}

    document.getElementById('adress').addEventListener('keyup', (e) =>{

        "use strict";

        const itemList = document.getElementById('itemList');
        const value=e.currentTarget.value;
        if(value==="" || !itemList.firstChild)
        {
            SubmitButton(0);
        }
        else
        {
            SubmitButton(1);
        }

    });


//The function orderForm() will get executed before submitting
document.getElementById('orderForm').addEventListener('submit', function() {
    orderForm();
});

function orderForm()
{
    "use strict";
    const itemList = document.getElementById('itemList');
    //const itemList = document.querySelectorAll('#itemList li');
    const form = document.getElementById('orderForm');


    // Create hidden fields for each item
    itemList.querySelectorAll('li').forEach((item, index) => {
        // Extract data from list item
        const id = item.getAttribute("data-articleId");

        // Create hidden input fields
        const newField = document.createElement('input');
        newField.type = 'hidden';
        newField.name = `item${index + 1}`;
        newField.value = id;

        // Append hidden fields to the form
        form.appendChild(newField);
    });
}

function checkButtonBacker(status)
{
    "use strict";
    const forms = document.querySelectorAll('.backerForm');

    forms.forEach(form => {
        // Select the first radio button in each form
        const radioButtons = form.querySelectorAll('input[type="radio"]');
        radioButtons[status].checked = true; // Check the first radio button
    });
}


function process(jsonString)
{

    "use strict";
    const orders = JSON.parse(jsonString);


    const itemList = document.getElementById('myOrders');
    while (itemList.firstChild) {
        itemList.firstChild.remove();
    }

    orders.forEach(item =>{
        const element=document.createElement('li');

        const status=getStatus(item.status);
        element.className='item';
        element.textContent=item.name+" : "+ status;

        itemList.appendChild(element);
    });


}
function getStatus(status)
{
    "use strict";
    switch (status)
    {
        case '0': return "Bestellt";
        case '1': return "Im Offen";
        case '2': return "Fertig";
        case '3': return "Unterwegs";
        case '4': return "Geliefert";
    }
}


function requestData() {
    "use strict";
    let request = new XMLHttpRequest(); // Create a new request object

    request.open("GET", "KundenStatus.php"); // URL for HTTP-GET
    request.onreadystatechange = function() {
        processData(request); // Pass the request object to processData
    };
    request.send(null); // Send the request
}

function processData(request) {
    "use strict";
    if (request.readyState == 4) { // Transmission = DONE
        if (request.status == 200) { // HTTP status = OK
            if (request.responseText != null) {
                process(request.responseText); // Process the data
            } else {
                console.error("Document is empty");
            }
        } else {
            console.error("Transmission failed");
        }
    }
}
function startPolling() {
    "use strict";
    // Polling alle 5 Sekunden starten
    window.setInterval(requestData, 2000); // 5000 Millisekunden = 5 Sekunden
}

function submit(id)
{
    const form=document.forms['$order_id'];
    form.submit();
}