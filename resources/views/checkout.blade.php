<?php
// Initialize variables for subtotal, discount, and total
$subtotal = 0;
$discount_points = 0;
$total = 0;

// Sample product list for demonstration, CAN BE DELETED
// $products = [
//     ['name' => 'Grapes', 'code' => '424240', 'qty' => 1, 'price' => 5000],
//     ['name' => 'Apple', 'code' => '042424', 'qty' => 1, 'price' => 10],
//     ['name' => 'Orange', 'code' => '4237433', 'qty' => 1, 'price' => 10],
// ];

$products = [];

// Calculate subtotal
foreach ($products as $product) {
    $subtotal += $product['price'] * $product['qty']; // Multiply price by quantity
}

$total = $subtotal; // Discount temporarily set to 0
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- ========================= HEAD SECTION ========================= -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dipensa Teknolohiya Grocery</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script> <!--import scannner-->
    <script src="{{ asset('js/apiHandler.js') }}"></script>
    <script>
        let token = null; // Global variable to store the Bearer token

        async function apiHandler(action, id, data = null) {
            const baseUrl = 'http://127.0.0.1:8001/api';

            let url = '';
            let method = '';
            let body = null;
            let headers = {
                'Content-Type': 'application/json',
            };

            // Helper function to fetch the Bearer token
            async function fetchToken() {
                if (!token) {
                    try {
                        const response = await fetch(`${baseUrl}/generate-token`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                        });

                        if (!response.ok) {
                            throw new Error(`Failed to generate token. Status: ${response.status}`);
                        }

                        const data = await response.json();
                        if (!data.token) {
                            throw new Error('Token not found in response');
                        }

                        token = data.token; // Store the token globally
                    } catch (error) {
                        console.error('Error generating token:', error);
                        return Promise.reject('Unable to generate token');
                    }
                }
            }

            // Determine the endpoint and HTTP method based on action
            switch (action) {
                case 'fetchLoyaltyCard':
                    url = `${baseUrl}/loyalty-cards/${id}`;
                    method = 'GET';
                    break;

                case 'updateLoyaltyCard':
                    url = `${baseUrl}/loyalty-cards/${id}`;
                    method = 'PUT';
                    body = JSON.stringify(data);
                    break;

                default:
                    console.error('Invalid action:', action);
                    return Promise.reject('Invalid action');
            }

            try {
                // Ensure we have a valid token before making the request
                await fetchToken();

                // Include the Bearer token in the Authorization header
                headers['Authorization'] = `Bearer ${token}`;

                const options = {
                    method,
                    headers,
                };

                // Include the body for PUT requests
                if (body) {
                    options.body = body;
                }

                const response = await fetch(url, options);

                // Handle response
                if (response.status === 404) {
                    console.warn('Resource not found (404):', url);
                    return null;
                }

                if (!response.ok) {
                    console.error(`HTTP Error: ${response.status}`);
                    throw new Error(`HTTP Error: ${response.statusText || 'Unknown error'}`);
                }

                const responseData = await response.json();

                // Return null if the response body is empty
                if (!responseData || Object.keys(responseData).length === 0) {
                    console.warn('Empty response body:', responseData);
                    return null;
                }

                return responseData;

            } catch (error) {
                console.error('API Error:', error.message);
                return Promise.reject(error.message);
            }
        }

    </script>

    
</head>
<body>
    
<div class="main-content">
    <!-- ========================= PRODUCT TABLE SECTION ========================= -->
    <div class="table-container">
        <div class="product-table">
            <table>
                <thead>
                    <tr>
                        <th>SELECT</th>
                        <th>NAME</th>
                        <th>CODE</th>
                        <th>QTY</th>
                        <th>PRICE</th>
                        <th>TOTAL</th>
                    </tr>
                </thead>
                <tbody id="product-table-body">
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><input type="checkbox" class="product-checkbox"></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['code']); ?></td>
                            <td class="qty"><?php echo $product['qty']; ?></td>
                            <td class="price"><?php echo "₱" . number_format($product['price'], 2); ?></td>
                            <td class="total-price"><?php echo "₱" . number_format($product['price'] * $product['qty'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>        
    </div>

    <!-- ========================= INPUT SECTION ========================= --> 
    <div class="input-section">
        <div id="input-loyalty-container">
            <div class="container-wrapper">
                
                <!---------------------- Manual Input Section ---------------------->
                <div id = "inputContainer">
                    <form method="POST" action="">
                        <label>Product Number</label>
                        <input type="number" id="product_code" name="product_number" placeholder="Enter Product Number">

                        <label>Quantity</label>
                        <input type="number" id="quantity" name="quantity" placeholder="Enter Quantity" min="1">

                        <div class="container-wrapper">
                            <button type="button" onclick="addProduct()" class="add-btn">Add to Cart</button> 
                            <button type="button" onclick="validateVoid()" class="delete-btn">🗑️</button> 
                        </div>
                        
                    </form>
                </div>
                
                <!---------------------- Scanner Section ---------------------->
                <div id="scanner-container">
                    <div id="video-preview-container">
                        <div id="video-preview"></div> <!-- Quagga will insert the video feed here -->
                    </div>
                    <div id="output">Scanned Barcode: <span id="barcode-result">None</span></div>
                </div>
            </div>

                <script>
                    //-------------------- SCANNER SCRIPT starts --------------------------
                    // Scanner Script
                    let isScanning = true; // Flag to control scanning delay

                    // Initialize QuaggaJS
                    Quagga.init({
                        inputStream: {
                            type: "LiveStream",
                            target: document.querySelector("#video-preview"), // Attach video to preview div
                            constraints: {
                                width: 1920, // High-resolution input
                                height: 1080,
                                facingMode: "environment" // Use rear camera
                            }
                        },
                        decoder: {
                            readers: ["code_128_reader", "ean_reader", "ean_8_reader"] // Supported barcode formats
                        }
                    }, function(err) {
                        if (err) {
                            console.error("Error initializing Quagga:", err);
                            return;
                        }
                        console.log("QuaggaJS initialized.");
                        Quagga.start(); // Start scanning
                    });

                    // Play a beep sound on successful scan
                    function playBeep() {
                        const beep = new Audio("{{ asset('Sound/beep.wav') }}");
                        beep.play();
                    }

                    // Handle barcode detection
                    Quagga.onDetected(function(data) {
                        if (!isScanning) return; // Skip if a scan is already in progress

                        isScanning = false; // Disable further scanning temporarily
                        const barcode = data.codeResult.code;
                        console.log("Barcode detected:", barcode);

                        // Check if the loyalty_card input is focused
                        if (document.activeElement === document.getElementById('loyalty_card')) {
                            // If focused, process the loyalty card (extract ID only)
                            const loyaltyCardId = barcode.split('-')[0]; // Get only the first part of the barcode
                            document.getElementById("loyalty_card").value = loyaltyCardId; // Set the value to loyalty card ID
                            loyaltyCardVerification();
                            // Optionally, you can send the loyaltyCardId to verify or fetch details
                            console.log("Loyalty Card ID:", loyaltyCardId);
                        } else {
                            // Otherwise, process the product barcode
                            document.getElementById("product_code").value = barcode; // Set the product barcode
                            document.getElementById("quantity").value = 1; // Set quantity to 1
                            addProduct(); // Add product to cart
                        }

                        // Play the beep sound
                        playBeep();

                        // Re-enable scanning after a 1-second delay
                        setTimeout(() => {
                            isScanning = true;
                        }, 1000);
                    });

                    //-------------------- SCANNER SCRIPT ends --------------------------
                </script>

            <!---------------------- Loyalty Section ---------------------->
            <div class="loyalty-section">
                <div class="loyalty-checkbox">
                    <input type="checkbox" id="loyaltybox" class="apply_loyalty">
                    <label for="loyalty">Use Loyalty Card</label>
                    <input type="number" id="loyalty_card" name="loyalty_card" placeholder="Enter Card Number"   disabled>
                    <button type="button" onclick="loyaltyCardVerification()" id="verifyButton" disable>Verify</button>
                </div>

                <div class="use-points">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="usePointsSwitch"  disable>
                        <label class="form-check-label" id="usePointsLabel" for="usePointsSwitch">Use Loyalty Points for Discounts?</label>
                    </div>
                </div>

                <div class="profile">
                    <div class="profile-left">
                        <div class="profile-icon">&#128100;</div>
                        <p class="loyalty-id">Loyalty ID: # <span id='cardNumber'>0000</span></p>     
                    </div> 
                
                    <div class="points">
                        <span>Points Balance: </span>
                        <span id="loyalty-points">3500</span>
                    </div>     
                </div>
            </div>

            <!------------------------------ Totals Section ------------------------------------->
            <div class="total-payment-containerWrapper">
                <div class="totals" style="margin-top: auto;">
                        <p>SUBTOTAL: <span id="subtotal">₱0.00</span></p>
                        <p>DISCOUNT POINTS: <span id="discount-points">0</span></p>
                        <hr/>
                        <p class="total">TOTAL: <span id="total">₱0.00</span></p>
                </div>
                    <br>
                    <!------------------------------ PAYMENT SECTION ---------------------------------------->
                <div class="payment-container">
                    <label for="cashReceived">Cash Received:</label>
                    <input type="number" id="cashReceived" placeholder="Enter amount received" step="0.01">

                    <label for="change">Change:</label>
                    <input type="number" id="change" value="0" readonly step="0.01">

                    <!-----------------------PAY BUTTONS--------------------------------------->
                    <div class="cancelTransacButton">
                        <button type="button" id="cancelTransac" onclick="cancelTransaction()" disabled>Cancel</button>
                    </div>
                    {{-- onsubmit="return confirmPayment()" --}}
                    <div class="paymentButtonContainer">
                        <form action="{{ route('transactions.store') }}" method="POST">
                            @csrf
                                <!-- PHP HERE FOR  PAYMENT LOG? -->
                            <?php foreach ($products as $product): ?>
                                <input type="hidden" name="products[]" value='<?php echo json_encode($product); ?>'>
                            <?php endforeach; ?>

                            <input type="hidden" name="order_date" value="{{ now()->toDateString() }}"> <!-- Or provide a specific order date -->
                            <input type="hidden" name="subtotal" value="{{ $subtotal }}"> <!-- Your subtotal calculation -->
                            <input type="hidden" name="total" value="{{ $total }}"> <!-- Your total calculation -->
                            <button type="submit" class="checkout-btn" id="checkout-btn" disabled>PAY</button>
                        </form>
                    </div>
                </div>    
            </div>
        </div>
    </div>


    <!-- ========================= MODALS ========================= -->
    <!-- Delete Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3>Admin Password Required</h3>
            <form id="deleteForm">
                <label for="adminPassword">Password</label>
                <input type="password" id="adminPassword" placeholder="Enter Password" required autocomplete="new-password">
                <div class="modal-buttons">
                    <button type="button" id="Confirm" onclick="validateDelete()">Confirm</button>
                    <button type="button" id="Cancel" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmation-modal" class="modal">
        <div class="modal-content">
            <p>Are you sure you want to use all your loyalty points?</p>
            <div class="modal-footer">
                <button id="confirm-yes" class="modal-button confirm">YES</button>
                <button id="confirm-no" class="modal-button cancel">NO</button>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal for Payment -->
    <div id="confirmation-Paymentmodal" class="modal">
        <div class="modal-content">
            <p>Are you sure you want to proceed with the payment?</p>
            <div class="button-container">
                <button id="confirmYes">Yes</button>
                <button id="confirmNo">No</button>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal for Transaction Cancelation -->
    <div id="cancel-modal" class="modal">
        <div class="modal-content">
            <p>Are you sure you want to cancel the current transaction?</p>
            <div class="button-container">
                <button id="cancelConfirmYes">Yes</button>
                <button id="cancelConfirmNo">No</button>
            </div>
        </div>
    </div>


    <!-- Success Modal -->
    <div id="success-modal" class="modal">
        <div class="modal-content">
            <p>Purchase Successful!</p>
            <div class="button-container">
                <button id="okButton">OK</button>
            </div>
        </div>
    </div>

</div>
<!---------------------------SCRIPT-------------------------------->
<script>

async function loyaltyCardVerification (){
    const loyaltyCardId = document.getElementById('loyalty_card').value;
    try {
        const loyaltyCard = await apiHandler('fetchLoyaltyCard', loyaltyCardId);
        verifiedExisting(loyaltyCard.Points);
    } catch (error) {
        alert("Error during loyalty card verification:", error);
        playError();
        document.getElementById('loyalty_card').style.backgroundColor = '#e74c3c';
        document.getElementById('usePointsSwitch').disabled = true;
        document.getElementById('usePointsSwitch').checked = false;
        document.getElementById('usePointsSwitch').style.display = 'none';
        document.getElementById('usePointsLabel').style.display = 'none';
        let elements = document.getElementsByClassName('profile');
        for (let i = 0; i < elements.length; i++) {
            elements[i].style.display = 'none';
        }
    }
}

async function updateLoyaltyCard(points) {
    const loyaltyCardId = document.getElementById('loyalty_card').value;

    try {
        const updatedLoyaltyCard = await apiHandler('updateLoyaltyCard', loyaltyCardId, { Points: points });
    } catch (error) {
        alert(error);
    }
}

//====== ADMIN PASSWORD VALIDATION ======
const adminPassword = "admin123"; // Replace with the actual admin password

//====== EVENT LISTENERS ======
document.getElementById('cashReceived').addEventListener('input', calculateChange);
document.getElementById('loyaltybox').addEventListener('change', handleLoyaltyBox);
document.getElementById('Cancel').addEventListener('click', closeModal);
document.getElementById('usePointsSwitch').addEventListener('change', handlePointsSwitch);
document.getElementById('loyalty_card').addEventListener('focus', handleLoyaltyCardFocus);


// Set up a MutationObserver to detect changes in the #total element
const observer = new MutationObserver(() => {
        handleCancelTransacBtn();
    });

// Start observing the #total element for text content changes
const totalElement = document.getElementById('total');
observer.observe(totalElement, { childList: true, subtree: true, characterData: true });

// Initialize the button state
handleCancelTransacBtn();

//====== LOYALTY BOX HANDLER ======
function handleLoyaltyBox() {
    
    document.getElementById('loyalty_card').value = '';
    document.getElementById('loyalty_card').style.backgroundColor = 'white';
    document.getElementById('usePointsSwitch').checked = false;

    if (document.getElementById('loyaltybox').checked) {
        document.getElementById('loyalty_card').disabled = false;
        document.getElementById('verifyButton').disabled = false;
        document.getElementById('verifyButton').style.display = 'block';
        document.getElementById('loyalty_card').style.display = 'block';
    } else {
        document.getElementById('loyalty_card').disabled = true;
        document.getElementById('usePointsSwitch').disabled = true;
        document.getElementById('verifyButton').disabled = true;
        document.getElementById('verifyButton').style.display = 'none';
        document.getElementById('loyalty_card').style.display = 'none';
        document.getElementById('usePointsSwitch').style.display = 'none';
        document.getElementById('usePointsLabel').style.display = 'none';
        let elements = document.getElementsByClassName('profile');
        for (let i = 0; i < elements.length; i++) {
            elements[i].style.display = 'none';
        }
    }
}


function verifiedExisting (points = 0) {
    //enable buttons
    document.getElementById('usePointsSwitch').style.display = 'block';
    document.getElementById('loyalty_card').style.backgroundColor = '#58b800';
    document.getElementById('usePointsLabel').style.display = 'block';
    let elements = document.getElementsByClassName('profile');
        for (let i = 0; i < elements.length; i++) {
            elements[i].style.display = 'flex';
        }

    document.getElementById('loyalty-points').innerText = points;
    document.getElementById('cardNumber').innerText = document.getElementById('loyalty_card').value;

    //will only enable usepoints if there's available points to use
    if (points != 0){
        document.getElementById('usePointsLabel').innerText = `Points Available: ${points}`;
        document.getElementById('usePointsSwitch').disabled = false;
    }

    //update profile
    document.getElementById('loyalty-points').innerText = points;
    document.getElementById('cardNumber').innerText = document.getElementById('loyalty_card').value;
}




//====== ERROR SOUND ======
function playError() {
    const error = new Audio("{{ asset('Sound/error.wav') }}");
    error.play();
}

//====== QUANTITY VALIDATION ======
function preventNegativeQuantity(input) {
    if (input.value < 0) {
        input.value = 0;
    }
}

//====== PAYMENT SUCCESS MODAL ======
function confirmPayment() {
    const confirmationModal = document.getElementById('confirmation-Paymentmodal');
    const confirmYes = document.getElementById('confirmYes');
    const confirmNo = document.getElementById('confirmNo');

    // Show the confirmation modal
    if (confirmationModal && confirmYes && confirmNo) {
        confirmationModal.style.display = 'flex';

        // Handle "Yes" click
        confirmYes.addEventListener('click', () => {
            confirmationModal.style.display = 'none'; // Close confirmation modal
            showPaymentSuccessMessage(); // Proceed to success modal
        });

        // Handle "No" click
        confirmNo.addEventListener('click', () => {
            confirmationModal.style.display = 'none'; // Close confirmation modal
        });

        return false; // Prevent form submission
    }

    return false; // Prevent form submission
}


function showPaymentSuccessMessage() {
    const successModal = document.getElementById('success-modal');

    if (successModal) {
        // Ensure modal is shown in the center
        successModal.style.display = 'flex';
        updatePointsAfterPayment();

        // Auto-close the modal after 2 seconds
        setTimeout(() => {
            successModal.style.display = 'none';
        }, 2000);
        newTranssaction();
        return false; // Prevent form submission
    }

    return false; // Prevent form submission
}


//====== CHECKOUT VALIDATION ======
function validateCheckout() {
    const rows = document.querySelectorAll('#product-table-body tr');
    if (rows.length === 0) {
        alert('No products in the cart. Please add items before proceeding to checkout.');
        return false;
    }
    return true;
}

//====== ADD PRODUCT FUNCTION ======
function addProduct() {
    const productCode = document.getElementById('product_code').value.trim();
    const quantity = parseInt(document.getElementById('quantity').value);

    if (!productCode) {
        alert('No product number entered. Please input a product number.');
        return;
    }

    if (quantity <= 0 || isNaN(quantity)) {
        alert('Please enter a valid quantity greater than 0.');
        return;
    }

    const rows = document.querySelectorAll('#product-table-body tr');
    let productExistsTable = false;
    let productExistsDB = false;
    let productName = '';
    let productPrice = 0;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/validate-product', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4) {
            if (xhr.status == 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.exists) {
                    productExistsDB = true;
                    productName = response.productName;
                    productPrice = parseFloat(response.productPrice);
                } else {
                    playError();
                    document.getElementById('errorMessage').style.display = 'block';
                    setTimeout(() => {
                        document.getElementById('errorMessage').style.display = 'none';
                    }, 2000);
                    return;
                }

                if (productExistsDB) {
                    rows.forEach(row => {
                        const codeCell = row.cells[2].textContent;
                        if (codeCell === productCode) {
                            const qtyCell = row.querySelector('.qty');
                            const newQty = parseInt(qtyCell.textContent) + quantity;
                            qtyCell.textContent = newQty;
                            row.querySelector('.total-price').textContent = `₱${(newQty * productPrice).toFixed(2)}`;
                            productExistsTable = true;
                        }
                    });

                    if (!productExistsTable) {
                        const row = document.createElement('tr');
                        row.innerHTML = `<td><input type="checkbox" class="product-checkbox"></td><td>${productName}</td><td>${productCode}</td><td class="qty">${quantity}</td><td class="price">₱${productPrice.toFixed(2)}</td><td class="total-price">₱${(productPrice * quantity).toFixed(2)}</td>`;
                        document.getElementById('product-table-body').appendChild(row);
                    }

                    updateTotals();
                }
            } else {
                console.error('Request failed with status ' + xhr.status);
            }
        }
    };

    xhr.send('product_code=' + productCode);
}

//====== DELETE VALIDATION ======
function validateDelete() {
    const checkboxes = document.querySelectorAll('.product-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('No products selected for deletion.');
    } else {
        validatePassword();
    }
}

//====== UPDATE TOTALS ======
function updateTotals() {
    let newSubtotal = 0;
    const rows = document.querySelectorAll('#product-table-body tr');

    rows.forEach((row, index) => {
        // Parse price and quantity safely
        const priceText = row.querySelector('.price').textContent.replace('₱', '').replace(/,/g, '').trim();
        const qtyText = row.querySelector('.qty').textContent.trim();

        const price = parseFloat(priceText);
        const qty = parseInt(qtyText);

        // Log for debugging
        console.log(`Row ${index + 1}: Price = ${price}, Qty = ${qty}`);

        // Ensure valid numeric values
        if (!isNaN(price) && !isNaN(qty)) {
            newSubtotal += price * qty;
        } else {
            console.error(`Invalid data in row ${index + 1}: Price = ${priceText}, Qty = ${qtyText}`);
        }
    });

    // Log final subtotal
    console.log(`New Subtotal: ${newSubtotal}`);

    // Update the subtotal and total
    document.getElementById('subtotal').textContent = `₱${newSubtotal.toFixed(2)}`;
    document.getElementById('total').textContent = `₱${newSubtotal.toFixed(2)}`;

    // Recalculate change
    calculateChange();
}


//====== CLEAR CART ======
function validateVoid(){
    const checkboxes = document.querySelectorAll('.product-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Select a product to void first.');
    } else {
        displayVoidModal();
    }
}


function displayVoidModal() {
    document.getElementById('deleteModal').style.display = 'flex';
    document.getElementById('adminPassword').value = '';
}

//====== CLOSE MODAL ======
function closeModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

//====== PASSWORD VALIDATION ======
function validatePassword() {
    const inputPassword = document.getElementById('adminPassword').value;

    if (inputPassword === adminPassword) {
        const checkboxes = document.querySelectorAll('.product-checkbox:checked');
        checkboxes.forEach(checkbox => checkbox.closest('tr').remove());
        updateTotals();
        document.getElementById('adminPassword').value = '';
        closeModal();
    } else {
        alert('Incorrect password!');
    }
}

//====== LOYALTY POINTS HANDLER ======
let pointsAdded = false;

function handlePointsSwitch(event) {
    const isChecked = document.getElementById('usePointsSwitch').checked;
    const subtotal = parseFloat(document.getElementById('subtotal').textContent.replace('₱', '').trim());
    const loyaltyPoints = parseInt(document.getElementById('loyalty-points').textContent.trim());
    let pointsUsed = parseInt(document.getElementById('discount-points').textContent.trim());

    if (isChecked) {
        // Apply points if not already applied
        if (pointsUsed === 0) {
            pointsUsed = Math.min(loyaltyPoints, subtotal); // Calculate points to be used
            document.getElementById('discount-points').textContent = pointsUsed;
            document.getElementById('total').textContent = `₱${(subtotal - pointsUsed).toFixed(2)}`; // Update total
        }
    } else {
        // Remove applied points and restore original total
        document.getElementById('discount-points').textContent = '0'; // Reset discount points
        document.getElementById('total').textContent = `₱${subtotal.toFixed(2)}`; // Restore original total
    }
}


//====== UPDATE POINTS AFTER PAYMENT ======
function updatePointsAfterPayment() {
    const finalTotal = parseFloat(document.getElementById('total').textContent.replace('₱', '').trim());
    const subtotal = parseFloat(document.getElementById('subtotal').textContent.replace('₱', '').trim());
    const loyaltyGain = Math.floor(finalTotal * 0.01); // Loyalty points gained based on subtotal (1%)
    let loyaltyPoints = parseInt(document.getElementById('loyalty-points').textContent.trim());
    const pointsUsed = parseInt(document.getElementById('discount-points').textContent.trim());

    // Deduct used points only if they were applied
    if (pointsUsed > 0) {
        loyaltyPoints -= pointsUsed;
    }

    // Always add 1% loyalty points based on the subtotal
    loyaltyPoints += loyaltyGain;

    // Update loyalty points display
    document.getElementById('loyalty-points').textContent = loyaltyPoints;

    // Reset discount points after payment
    document.getElementById('discount-points').textContent = '0';

    updateLoyaltyCard(loyaltyPoints);
}


//====== UPDATE TOTAL AFTER DISCOUNT ======
function updateTotalAfterDiscount(points) {
    const subtotal = parseFloat(document.getElementById('subtotal').textContent.replace('₱', '').trim());
    let discountAmount = points;

    if (discountAmount > subtotal) {
        discountAmount = subtotal - 1;
    }

    const newTotal = subtotal - discountAmount;

    if (newTotal < 0) {
        alert('The discount exceeds the total amount.');
        return;
    }

    document.getElementById('total').textContent = `₱${newTotal.toFixed(2)}`;
}


//====== CALCULATE CHANGE ======
function calculateChange() {
    const finalAmount = parseFloat(document.getElementById('total').textContent.replace('₱', ''));
    let cashReceived = parseFloat(document.getElementById('cashReceived').value);

    if (cashReceived < 0) {
        cashReceived = 0;
        document.getElementById('cashReceived').value = "0.00";
        document.getElementById('checkout-btn').disabled = true;
    }

    if (!isNaN(cashReceived) && cashReceived >= finalAmount) {
        const change = cashReceived - finalAmount;
        document.getElementById('change').value = change.toFixed(2);
        document.getElementById('checkout-btn').disabled = false;
    } else {
        document.getElementById('change').value = "0.00";
        document.getElementById('checkout-btn').disabled = true;
    }
}
//===========================CANCEL TRANSACTION================================
function handleCancelTransacBtn() {
        const totalElement = document.getElementById('total');
        const total = parseFloat(totalElement.textContent.replace('₱', '').trim());

        // Enable or disable the button based on the total amount
        document.getElementById('cancelTransac').disabled = total === 0;
    }

function cancelTransaction() {
    const cancelModal = document.getElementById('cancel-modal');
    const cancelConfirmYes = document.getElementById('cancelConfirmYes');
    const cancelConfirmNo = document.getElementById('cancelConfirmNo');

    // Show the confirmation modal
    if (cancelModal && cancelConfirmYes && cancelConfirmNo) {
        cancelModal.style.display = 'flex';

        // Handle "Yes" click
        cancelConfirmYes.addEventListener('click', () => {
            cancelModal.style.display = 'none'; // Close confirmation modal
            newTranssaction(); // Proceed to success modal
        });

        // Handle "No" click
        cancelConfirmNo.addEventListener('click', () => {
            cancelModal.style.display = 'none'; // Close confirmation modal
        });

        return false; // Prevent form submission
    }

    return false; // Prevent form submission
}

//===========================RESET TRANSACTION=================================
//function that resets the transaction  after
function newTranssaction(){
    document.getElementById('loyaltybox').checked = false;
    handleLoyaltyBox();
    document.getElementById('product_code').value = '';
    document.getElementById('quantity').value = '';
    document.getElementById('product-table-body').innerHTML = '';
    document.getElementById('subtotal').textContent = '₱0.00';
    document.getElementById('total').textContent = '₱0.00';
    document.getElementById('change').value = '0.00';
    document.getElementById('cashReceived').value = '';
    document.getElementById('discount-points').textContent = '0';
    document.getElementById('loyalty-points').textContent = '0';
    document.getElementById('adminPassword').value = '';
    document.getElementById('checkout-btn').disabled = true;
    document.getElementById('deleteModal').style.display = 'none';
    pointsAdded = false;
    document.getElementById('discount-points-input').value = '';
    document.getElementById('discount-points-btn').disabled = true;
}


//====== INITIALIZATION ======
window.onload = updateTotals;

</script>

</body>
</html>
