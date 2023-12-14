const env = require('dotenv').config()
const constants = require("../config/constants")
var mongoose = require("mongoose");
var ReportData = require("../models/ReportDataModel");

mongoose.connect(process.env.DataBaseUrl, { useNewUrlParser: true, useUnifiedTopology: true }).then(() => {
    console.log("connection open !!");
}).catch((err) => {
    console.log("err", err);
    console.error(err);
});

const dataToInsert = [
  { reason: "I just don't like it" },
  { reason: "it's spam" },
  { reason: "Nudity or sexual activity" },
  { reason: "Hate speech or symbols" },
  { reason: "False information" },
  { reason: "Bullying or harassment" },
  
  
];

const seedDB = async () => {

    await ReportData.insertMany(dataToInsert);
};

seedDB().then(() => {
    mongoose.connection.close()
})


