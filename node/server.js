var express = require('express');
var bodyParser = require('body-parser');

var app = express();
var cors = require('cors')
app.use(bodyParser.json())
app.use(bodyParser.urlencoded({ extended: true }))
app.use(express.urlencoded({ extended: true }));
app.disable('etag');


var corsOptions = {
    origin: ["http://localhost:3001", "http://localhost:3000"]
};
// app.use(fileUpload());
app.use(cors(corsOptions));


// Database Config
var dbConfig = require('./config/database.js');

var mongoose = require('mongoose');
mongoose.set('strictQuery', false);
mongoose.connect(dbConfig.url);
mongoose.connection.once('open', function () {
    console.log("Successfully connected to the database");
})
//end Database config

require('./routes/PostRoutes.js')(app);

app.listen(9000, function () {
    console.info("Server is listening on port 8004");
});

