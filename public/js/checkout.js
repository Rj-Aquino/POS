
//------------------------Other Scripts -----------------------------
const adminPassword = "admin123"; // Replace with the actual admin password

function preventNegativeQuantity(input) {
    if (input.value < 0) {
        input.value = 0;
    }
}

function showPaymentSuccessMessage() {
// Show the payment successful modal
const successModal = document.getElementById('success-modal');
successModal.style.display = 'flex';

// Prevent the form from submitting immediately (you can submit it after the success message is shown)
setTimeout(() => {
    // Optionally, you can automatically redirect to another page after showing the success message.
    // window.location.href = "nextPageUrl";  // Redirect after success
}, 2000); // Wait for 2 seconds before doing anything (can be adjusted)

return false; // Prevent form submission (you can handle actual form submission after success message is shown)
}


function validateCheckout() {
    const rows = document.querySelectorAll('#product-table-body tr');
    
    if (rows.length === 0) {
        alert('No products in the cart. Please add items before proceeding to checkout.');
        return false;
    }
    
    return true;
}

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

    // Get the CSRF token from the meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Make an AJAX request to check if the product code (id) exists in the database
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/validate-product', true);  // Ensure this route exists in your Laravel app
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    // Set the CSRF token in the request headers
    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4) {
            if (xhr.status == 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.exists) {
                    productExistsDB = true;
                    productName = response.productName; // Product name from database
                    productPrice = parseFloat(response.productPrice); // Product price from database
                    alert('Product exists!');
                } else {
                    alert('Product code does not exist.');
                    productExistsDB = false;
                }

                // Only proceed to check the table and add/update rows if product exists in DB
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

                    // If the product doesn't exist in the table, add a new row
                    if (!productExistsTable) {
                        const row = document.createElement('tr');
                        row.innerHTML = `<td><input type="checkbox" class="product-checkbox"></td><td>${productName}</td><td>${productCode}</td><td class="qty">${quantity}</td><td class="price">₱${productPrice.toFixed(2)}</td><td class="total-price">₱${(productPrice * quantity).toFixed(2)}</td>`;
                        document.getElementById('product-table-body').appendChild(row);
                    }

                    // Update totals after adding or updating the table
                    updateTotals();
                    document.querySelector('.checkout-btn').disabled = false;
                }
            } else {
                console.error('Request failed with status ' + xhr.status);
            }
        }
    };

    xhr.send('product_code=' + productCode);  // Send the product_code entered by the user
}


function validateDelete() {
    const checkboxes = document.querySelectorAll('.product-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('No products selected for deletion.');
    } else {
        validatePassword();
    }
}

function updateTotals() {
    let newSubtotal = 0;
    const rows = document.querySelectorAll('#product-table-body tr');

    rows.forEach(row => {
        const price = parseFloat(row.querySelector('.price').textContent.replace('₱', ''));
        const qty = parseInt(row.querySelector('.qty').textContent);
        newSubtotal += price * qty;
    });

    document.getElementById('subtotal').textContent = `₱${newSubtotal.toFixed(2)}`;
    document.getElementById('total').textContent = `₱${newSubtotal.toFixed(2)}`;

    // Enable the checkout button if there are rows in the table
    const checkoutButton = document.querySelector('.checkout-btn');
    checkoutButton.disabled = rows.length === 0;

    // Recalculate the change based on the updated total
    calculateChange();
}

function clearCart() {
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeSuccessModal() {
document.getElementById('success-modal').style.display = 'none';
}

// Function to close the modal
function closeModal() {
    document.getElementById('deleteModal').style.display = 'none'; // Hide the modal
}

// Event listener for the Cancel button
document.getElementById('Cancel').addEventListener('click', function() {
    closeModal(); // Close the modal when Cancel button is clicked
});


function validatePassword() {
    const inputPassword = document.getElementById('adminPassword').value;

    if (inputPassword === adminPassword) {
        const checkboxes = document.querySelectorAll('.product-checkbox:checked');
        checkboxes.forEach(checkbox => checkbox.closest('tr').remove());

        // Update totals after deleting products
        updateTotals();

        // Clear the password field after successful validation
        document.getElementById('adminPassword').value = '';
        closeModal(); // Close the modal after validation
    } else {
        alert('Incorrect password!');
    }
}

document.getElementById('use-points-yes').addEventListener('click', function(event) {
event.preventDefault(); // Prevent form submission
let loyaltyPoints = parseInt(document.getElementById('loyalty-points').textContent.trim());
const subtotal = parseFloat(document.getElementById('subtotal').textContent.replace('₱', '').trim());

// Check if the loyalty points will result in a negative total
if (loyaltyPoints > subtotal) {
    alert('You cannot apply more loyalty points than the subtotal. Please adjust the points.');
    return; // Do not apply the points if it would result in a negative total
}

// Apply loyalty points discount
document.getElementById('discount-points').textContent = loyaltyPoints;
updateTotalAfterDiscount(loyaltyPoints); // Function to update the total after applying loyalty points

// Update loyalty points to zero after usage
document.getElementById('loyalty-points').textContent = '0';

closeModal(); // Close the modal after applying points
});

let pointsAdded = false; // Flag to track if points were already added

document.getElementById('use-points-no').addEventListener('click', function(event) {
event.preventDefault(); // Prevent form submission

if (pointsAdded) {
    alert('Loyalty points have already been applied!');
    closeModal(); // Close the modal without applying points
    return;
}

// Get the final total (after applying any potential loyalty points)
const total = parseFloat(document.getElementById('total').textContent.replace('₱', '').trim());

// Calculate loyalty points as 1% of the total
const loyaltyGain = Math.floor(total * 0.01); // 1% of the total, rounded down to the nearest whole number

// Update loyalty points display with the newly calculated points
const currentPoints = parseInt(document.getElementById('loyalty-points').textContent.trim());
const newPoints = currentPoints + loyaltyGain; // Add calculated points to current points
document.getElementById('loyalty-points').textContent = newPoints;

// Optionally, reset discount points to 0 since no loyalty points were used
document.getElementById('discount-points').textContent = '0';

pointsAdded = true; // Set the flag to true after applying points

closeModal(); // Close the modal without applying points
});


// Function to update the total with discount points
function updateTotalAfterDiscount(points) {
const subtotal = parseFloat(document.getElementById('subtotal').textContent.replace('₱', '').trim());
let discountAmount = points; // Loyalty points being applied as discount

// If the points are greater than the subtotal, only apply enough to cover the subtotal
if (discountAmount > subtotal) {
    discountAmount = subtotal - 1;  // Ensure at least 1 peso is paid
}

const newTotal = subtotal - discountAmount;

if (newTotal < 0) {
    alert('The discount exceeds the total amount. Please adjust the points.');
    return; // Do not apply the discount if it results in a negative total
}

// Update total with the new discount
document.getElementById('total').textContent = `₱${newTotal.toFixed(2)}`;
}

function calculateChange() {
    const finalAmount = parseFloat(document.getElementById('total').textContent.replace('₱', ''));
    let cashReceived = parseFloat(document.getElementById('cashReceived').value);

    // Ensure cash received is not negative
    if (cashReceived < 0) {
        cashReceived = 0;
        document.getElementById('cashReceived').value = "0.00"; // Reset the input field to zero if negative value entered
    }

    if (!isNaN(cashReceived) && cashReceived >= finalAmount) {
        const change = cashReceived - finalAmount;
        document.getElementById('change').value = change.toFixed(2);  // Round the change to 2 decimal places
    } else {
        document.getElementById('change').value = "0.00"; // Show zero change if cash is less than final amount
    }
}

// Event listeners
document.getElementById('cashReceived').addEventListener('input', calculateChange);
document.getElementById('loyalty').addEventListener('change', () => {
document.getElementById('loyalty_card').disabled = !document.getElementById('loyalty').checked;
});

window.onload = updateTotals;

//-------------------------------Script Ends Here -------------------------------------------