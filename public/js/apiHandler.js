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

// Arguments:

// action(string value) = either['fetchCustomer', 'fetchLoyaltyCard', 'updateLoyaltyCard']
// id(int value) = id para maget ng loyalty-card or update ng loyaltycard
// data(JSON format) = Points: no of points

// Example Calls:
// For Getting LoyaltyCard
// const loyaltyCard = await apiHandler('fetchLoyaltyCard', loyaltyCardId);

// For Updating Points
// const updatedLoyaltyCard = await apiHandler('updateLoyaltyCard', loyaltyCardId, { Points: points });

// Return Values:
// JSON Format ang Return Value nito. nandon na lahat ng values na nakuha

// para sa checking kung existing, check for empty or null. nag rereturn siya ng null kung walang nakuha.
// kung meron, makukuha mo na don yung id, points, at yung iba pang kailangan 

// Para makuha yung value

// const loyaltycard = await apiHandler('fetchLoyaltyCard', LoyaltyCardID);

// loyaltycard.LoyaltyCardID = Yung Loyalty Card ID
// loyaltycard.Firstname = Yung Firstname

// etc...

// Sa Update, walang nang gagalawin sa JSON na Return Value.