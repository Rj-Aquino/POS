<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Update Loyalty Card Points</title>
    
    <!-- Include the JS file from public/js -->
    <script src="{{ asset('js/apiHandler.js') }}"></script>
</head>
<body>
    <h1>Update Loyalty Card Points</h1>

    <label for="loyalty-card-id">Enter Loyalty Card ID:</label>
    <input type="number" id="loyalty-card-id" placeholder="Enter Loyalty Card ID" required>

    <label for="loyalty-card-points">Enter Points:</label>
    <input type="number" id="loyalty-card-points" placeholder="Enter Points" required>

    <button onclick="updateLoyaltyCard()">Update Points</button>

    <div id="loyalty-card-details">Loyalty card details will appear here after update.</div>
    <div id="error-message"></div>

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

        async function updateLoyaltyCard() {
            const loyaltyCardId = document.getElementById('loyalty-card-id').value;
            const points = document.getElementById('loyalty-card-points').value;

            try {
                const updatedLoyaltyCard = await apiHandler('updateLoyaltyCard', loyaltyCardId, { Points: points });
                renderUpdatedLoyaltyCard(updatedLoyaltyCard);
            } catch (error) {
                displayError(error);
            }
        }

        // Function to render the updated loyalty card data
        function renderUpdatedLoyaltyCard(loyaltyCard) {
            const loyaltyCardDetails = document.getElementById('loyalty-card-details');
            loyaltyCardDetails.innerHTML = ''; // Clear previous content

            if (!loyaltyCard || !loyaltyCard.LoyaltyCardID) {
                loyaltyCardDetails.innerHTML = '<p>Failed to update loyalty card. Try again later.</p>';
                return;
            }

            loyaltyCardDetails.innerHTML = `
                <strong>Loyalty Card ID:</strong> ${loyaltyCard.LoyaltyCardID} <br>
                <strong>Updated Points:</strong> ${loyaltyCard.Points}
            `;
        }

        // Function to handle displaying error messages
        function displayError(message) {
            const errorDiv = document.getElementById('error-message');
            errorDiv.innerHTML = `<p>Error: ${message}</p>`;
        }
    </script>
</body>
</html>
