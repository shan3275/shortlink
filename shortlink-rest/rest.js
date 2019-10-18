const https			= require('https');
const fs			= require('fs');
const mgClient		= require('mongodb').MongoClient;
const express		= require('express');
const bodyParser	= require('body-parser');
const apis			= require('./apis');
const compression 	= require('compression');

var db;

const app = express();
let currentRequest = {};
app.use(bodyParser.urlencoded({extended: true}));

process.on('unhandledRejection', (reason, p) => {
	console.log('Unhandled Rejection at: Promise', p, 'reason:', reason, currentRequest);
});

app.use(compression());

https.createServer({
	key: fs.readFileSync('server.key'),
	cert: fs.readFileSync('server.crt')
}, app).listen(8830);
process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0';

//mgClient.connect('mongodb://{user}:{pass}@{host}:{port}/{dbname}', {
mgClient.connect('mongodb://localhost:27017', {
	reconnectTries: 60,
	reconnectInterval: 1000,
	useNewUrlParser: true,
	useUnifiedTopology: true
}, (err, database) => {
	if(!err){
		db = database.db('short');
		console.log('connected to database');
		apis.addApis(app, db);
	}
});
