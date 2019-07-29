var http = require('https');
var url = require('url');
var WebsocketServer = require('websocket').server;

//Se algum usuario se conectar o http.createServer vai ser execultado
var server = http.createServer(function(request,response) {

	function getPostParams(request, callback) {
	    var qs = require('querystring');
	    if (request.method == 'POST') {
	        var body = '';

	        request.on('data', function (data) {
	            body += data;
	            if (body.length > 1e6)
	                request.connection.destroy();
	        });

	        request.on('end', function () {
	            var POST = qs.parse(body);
	            callback(POST);
	        });
	    }
	}
    // in-server request from PHP
       if (request.method === "POST") {
    	getPostParams(request, function(POST) {	
			messageClients(POST.data);
			response.writeHead(200);
			response.end();
		});
		return;
	}
});
server.listen(8081);
console.log('Server Listening port:'+'8081');

/* Requests */
var websocketServer = new WebsocketServer({
	httpServer: server
});

websocketServer.on("request", websocketRequest);

// websockets storage
global.clients = {}; // store the connections
var connectionId = 0; // incremental unique ID for each connection (this does not decrement on close)
function websocketRequest(request) {
	// start the connection
	var connection = request.accept(null, request.origin); 
	connectionId++;
	// save the connection for future reference
	clients[connectionId] = connection;
}
// sends message to all the clients
function messageClients(message) {
	for (var i in clients) {
		clients[i].sendUTF(message);
	}
}