# How to set up API locally

First things first, make sure you have environment variables set up in appache. There are tons of guides on how you can do that, but essentially you have to allow appache to override files and configs inside of the "root" folder of your website. 
![](https://i.imgur.com/CDnBhQM.png)

You can do this by adding "Directory" to the default.config (which is usually located at /etc/appache2/sites-available/).
![](https://i.imgur.com/fRD5ssY.png)

Then you will need to create .htaccess on your local device at the "root" folder of your website and add the following environment variables with values corresponding to your server setup.
![](https://i.imgur.com/JRTAgiB.png)

After that, you need to restart your apache server for changes to take effect. 

# Testing

For testing purposes, I recommend using Postman because it's easy to use and allows you to test everything you want in a non-complicated way.

Before I provide examples of request formats for the endpoints, I have to mention the difference between GET and POST requests. 

A GET request uses the "query" format to send a request to the API. A query usually starts after the "?" sign in the URL. For example, "localhost.com/API/search.php?searchParameter=test" can be used to send a GET request to the search.php endpoint with a single parameter "searchParameter" which has a value of "test".

In Postman, you can send GET requests by selecting "GET" as the type of the request and adding parameters to your request.

![](https://i.imgur.com/HqQzNaU.png)

A POST request uses the "form-data" format inside of the "Body" to send a request to the API. The data inside of that "Body" is usually represented as regular Key:Value pairs.

In Postman, you can send POST requests by selecting "POST" as the type of the request, then selecting "Body" and "form-data" as the format of the "Body".

![](https://i.imgur.com/MRdAcTq.png)

### Your API supports several public endpoints:

1. Login.php (GET)
2. Register.php (POST)
3. AddContact.php (POST)
4. SearchContact.php (GET)
5. UpdateContact.php (POST)
6. DeleteContact.php (POST)

Examples:

#### 1. Login.php (GET)
![](https://i.imgur.com/AGiLEgV.png)

##### Login Endpoint Response Codes:

1. OK - 200 (Upon successful operation)
2. NOT FOUND - 404 (Missing request body / User doesn't exist / Incorrect password)

#### 2. Register.php (POST)
![](https://i.imgur.com/a677qW0.png)

##### Register Endpoint Response Codes:

1. CREATED - 201 (Upon successful operation)
2. NO CONTENT - 204 (Missing request body)
3. CONFLICT - 409 (User already exists / Couldn't create user)

#### 3. AddContact.php (POST)
![](https://i.imgur.com/mWBlEK5.png)

##### AddContact Endpoint Response Codes:

1. CREATED - 201 (Upon successful operation)
2. NO CONTENT - 204 (Missing request body)
3. CONFLICT - 409 (Couldn't create contact)

#### 4. SearchContact.php (GET)
![](https://i.imgur.com/NYu61so.png)

##### SearchContact Endpoint Response Codes:

1. OK - 200 (Upon successful operation)
2. NOT FOUND - 404 (Missing request body / Couldn't find Contact)

#### 5. UpdateContact.php (POST)
![](https://i.imgur.com/o6NfRyE.png)

##### UpdateContact Endpoint Response Codes:

1. OK - 200 (Upon successful operation)
2. NOT CONTENT - 204 (Missing request body)
3. NOT FOUND - 404 (Contact doesn't exist)

#### 6. DeleteContact.php (POST)
![](https://i.imgur.com/0bFEqzp.png)

##### DeleteContact Endpoint Response Codes:

1. OK - 200 (Upon successful operation)
2. NO CONTENT - 204 (Missing request body)
3. NOT FOUND - 404 (Contact doesn't exist)
